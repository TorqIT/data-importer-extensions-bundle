<?php

declare(strict_types=1);

namespace TorqIT\DataImporterExtensionsBundle\Mapping\Operator\Simple;

use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Bundle\DataImporterBundle\Mapping\Operator\AbstractOperator;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'pimcore.datahub.data_importer.operator', attributes: ['type' => 'arrayVal'])]
class ArrayVal extends AbstractOperator
{
    protected int|string $index;
    protected bool $recursiveSearch;
    protected bool $returnNullIfNotFound;

    public function setSettings(array $settings): void
    {
        $this->index = $settings['index'] ?? 0;
        $this->recursiveSearch = $settings['recursiveSearch'] ?? false;
        $this->returnNullIfNotFound = $settings['returnNullIfNotFound'] ?? false;
    }

    public function process($inputData, bool $dryRun = false)
    {
        if (!is_array($inputData) && !empty($inputData)) {
            $inputData = [$inputData];
        }
        if ($this->recursiveSearch) {
            return $this->recursiveSearchFunc($inputData);
        }
        if (array_key_exists($this->index, $inputData)) {
            return $inputData[$this->index];
        }
        if (is_array($inputData) && empty($inputData)) {
            return null;
        }
        if ($this->returnNullIfNotFound) {
            return null;
        }
        throw new InvalidConfigurationException("There is no key $this->index in given array");
    }

    private function recursiveSearchFunc(array $arr)
    {
        foreach ($arr as $key => $val) {
            if ($key == $this->index) {
                return $val;
            }
            if (is_array($val)) {
                $result = $this->recursiveSearchFunc($val);
                if ($result !== $val) {
                    return $result;
                }
            }
        }
        return $arr;
    }

    public function evaluateReturnType(string $inputType, int $index = null): string
    {
        if ($inputType === 'array') {
            if ($this->recursiveSearch) {
                return 'array';
            }
            return 'default';
        }
        throw new InvalidConfigurationException('Input must be an array!');
    }
}
