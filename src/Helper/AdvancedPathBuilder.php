<?php

namespace TorqIT\DataImporterExtensionsBundle\Helper;

use Pimcore\Model\Element\Service as ElementService;

class AdvancedPathBuilder{

    public static function buildPath(array $inputData, string $path, string $type='object'): string{

        // Protect content inside curly braces from being split on '/'
        $protected = [];
        $safePath = preg_replace_callback('/\{((?:[^{}]|\{[^{}]*\})*)\}/', function($m) use (&$protected) {
            $key = '___BRACES_' . count($protected) . '___';
            $protected[$key] = $m[0];
            return $key;
        }, $path);

        $parts = explode('/', $safePath);

        // Restore protected curly brace content
        foreach ($parts as $i => $part) {
            foreach ($protected as $placeholder => $original) {
                if (str_contains($part, $placeholder)) {
                    $parts[$i] = str_replace($placeholder, $original, $parts[$i]);
                }
            }
        }

        foreach($parts as $partIndex => $part){

            $matches = array();

            preg_match_all('/\$((\[[0-9A-Za-z_\-\ ]+\])+)((?:\{(?:[^{}]|\{[^{}]*\})*\})*)/', $part, $matches);

            if(count($matches) && count($matches[0])){
                foreach($matches[0] as $mIndex => $m){
                    //get keys out of array string
                    $keyString = $matches[1][$mIndex];
                    $keys = explode(',',str_replace(']', '', str_replace('[', '', str_replace('][', ',', $keyString))));
                    $val = $inputData;
                    foreach($keys as $k){
                        $val = $val[$k];
                    }

                    // Apply optional regex operations from chained {} blocks
                    if (!empty($matches[3][$mIndex])) {
                        preg_match_all('/\{((?:[^{}]|\{[^{}]*\})*)\}/', $matches[3][$mIndex], $opMatches);
                        foreach ($opMatches[1] as $operation) {
                            if (str_starts_with($operation, 's') && strlen($operation) >= 4) {
                                $substitution = self::parseSubstitutionPattern($operation);
                                if ($substitution !== null) {
                                    $val = preg_replace($substitution['pattern'], $substitution['replacement'], (string)$val, $substitution['limit']);
                                }
                            } else {
                                $pattern = self::parseRegexPattern($operation);
                                if ($pattern !== null && preg_match($pattern, (string)$val, $regexMatches)) {
                                    $val = trim($regexMatches[1] ?? $regexMatches[0]);
                                }
                            }
                        }
                    }

                    $parts[$partIndex] = str_replace($m, $val, $parts[$partIndex]);
                }
            }

            $parts[$partIndex] = ElementService::getValidKey($parts[$partIndex], $type);
        }

        $path = implode('/',$parts);

        return $path;

    }

    /**
     * Parse a substitution pattern like s/pattern/replacement/flags.
     * Returns an array with 'pattern', 'replacement', and 'limit' keys, or null if invalid.
     */
    private static function parseSubstitutionPattern(string $str): ?array
    {
        if (strlen($str) < 4 || $str[0] !== 's') {
            return null;
        }

        $delimiter = $str[1];
        $len = strlen($str);
        $pos = 2;
        $inCharClass = false;

        // Find end of search pattern
        while ($pos < $len) {
            $char = $str[$pos];
            if ($char === '\\' && $pos + 1 < $len) {
                $pos += 2;
                continue;
            }
            if ($inCharClass) {
                if ($char === ']') {
                    $inCharClass = false;
                }
            } elseif ($char === '[') {
                $inCharClass = true;
            } elseif ($char === $delimiter) {
                break;
            }
            $pos++;
        }

        if ($pos >= $len) {
            return null;
        }

        $pattern = substr($str, 2, $pos - 2);
        $pos++;

        // Find end of replacement
        $replStart = $pos;
        while ($pos < $len) {
            $char = $str[$pos];
            if ($char === '\\' && $pos + 1 < $len) {
                $pos += 2;
                continue;
            }
            if ($char === $delimiter) {
                break;
            }
            $pos++;
        }

        $replacement = substr($str, $replStart, $pos - $replStart);
        $flags = ($pos < $len) ? substr($str, $pos + 1) : '';

        // 'g' flag controls limit; strip it from PCRE flags
        $limit = str_contains($flags, 'g') ? -1 : 1;
        $pcreFlags = str_replace('g', '', $flags);

        return [
            'pattern' => $delimiter . $pattern . $delimiter . $pcreFlags,
            'replacement' => $replacement,
            'limit' => $limit,
        ];
    }

    /**
     * Parse a regex pattern like /^([^>]+)/ or /pattern/flags,
     * correctly handling character classes (e.g. [^>,/]) and escaped characters.
     * Returns the full PCRE pattern string or null if invalid.
     */
    private static function parseRegexPattern(string $regexStr): ?string
    {
        if (strlen($regexStr) < 2) {
            return null;
        }

        $delimiter = $regexStr[0];
        $len = strlen($regexStr);
        $pos = 1;
        $inCharClass = false;

        // Find the closing delimiter, respecting escapes and character classes
        while ($pos < $len) {
            $char = $regexStr[$pos];

            if ($char === '\\' && $pos + 1 < $len) {
                $pos += 2;
                continue;
            }

            if ($inCharClass) {
                if ($char === ']') {
                    $inCharClass = false;
                }
            } elseif ($char === '[') {
                $inCharClass = true;
            } elseif ($char === $delimiter) {
                // Found closing delimiter — remainder is flags
                $flags = substr($regexStr, $pos + 1);
                return substr($regexStr, 0, $pos + 1) . $flags;
            }

            $pos++;
        }

        return null;
    }
}
