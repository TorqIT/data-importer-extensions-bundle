<?php

namespace TorqIT\DataImporterExtensionsBundle\Mapping\Operator\Simple;

use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Bundle\DataImporterBundle\Mapping\Operator\AbstractOperator;

class Slugify extends AbstractOperator
{

    public function process($inputData, bool $dryRun = false)
    {
        if (!$inputData) {
            return $inputData;
        }
        if (!is_string($inputData)) {
            throw new InvalidConfigurationException("Input data must be of type string!");
        }
        $output = preg_replace('/[^A-Za-z0-9 -]+/', '', trim($inputData));
        $output = preg_replace('/\s+/', '-', $output);
        return mb_strtolower($output);
    }

    public function evaluateReturnType(string $inputType, ?int $index = null): string
    {
        return 'default';
    }
}