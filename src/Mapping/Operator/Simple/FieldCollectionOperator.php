<?php

namespace TorqIT\DataImporterExtensionsBundle\Mapping\Operator\Simple;

use Pimcore\Bundle\DataImporterBundle\Mapping\Operator\AbstractOperator;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;
use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;

class FieldCollectionOperator extends AbstractOperator
{
    protected string $fieldCollectionType;
    protected array $fieldMappings = [];
    protected array $fieldIndexMappings = [];

    public function setSettings(array $settings): void
    {
        if (empty($settings['fieldCollectionKey'])) {
            throw new InvalidConfigurationException("Please select a field collection from 'Field Collection:' dropdown.");
        }

        $this->fieldCollectionType = $settings['fieldCollectionKey'];
        $this->fieldMappings = $settings['fieldMappings'] ?? [];
        foreach (($settings['fieldMappings'] ?? []) as $fieldName => $index) {
            if (empty($index) && $index !== "0") {
                throw new InvalidConfigurationException("as Field Collection field mappings need to be indexes of inputted array data.");
            }
            $this->fieldIndexMappings[$index] = $fieldName;
        }

        if (!class_exists($this->getFieldCollectionClass())) {
            throw new InvalidConfigurationException("Field collection class '{$this->getFieldCollectionClass()}' does not exist.");
        }
    }

    /**
     * @param mixed $inputData
     * @param bool $dryRun
     *
     * @return array|false|mixed|null
     */
    public function process($inputData, bool $dryRun = false)
    {
        $fieldCollection = new Fieldcollection();
        if (empty($inputData)) {
            return $fieldCollection;
        }
        if (!is_array($inputData)) {
            $inputData = [$inputData];
        }

        if ($this->isTwoDeepArray($inputData)) {
            foreach ($inputData as $fcData) {
                if (!empty($fcData)) {
                    $fieldCollection->add($this->createFieldCollection($fcData));
                }
            }
        } else {
            if (!empty($inputData)) {
                $fieldCollection->add($this->createFieldCollection($inputData));
            }
        }

        return $fieldCollection;
    }

    private function createFieldCollection(array $inputData): AbstractData
    {
        $className = $this->getFieldCollectionClass();
        /** @var AbstractData $fcItem */
        $fcItem = new $className();
        foreach ($this->fieldIndexMappings as $index => $fieldName) {
            $setter = 'set' . ucfirst($fieldName);
            if (method_exists($fcItem, $setter)) {
                $fcItem->$setter($inputData[$index]);
            }
        }
        return $fcItem;
    }

    private function isTwoDeepArray($inputData)
    {
        // Check if it's a flat array (all values are not arrays)
        $allNotArrays = true;
        $allArrays = true;
        foreach ($inputData as $value) {
            if (is_array($value)) {
                $allNotArrays = false;
            } else {
                $allArrays = false;
            }
        }

        if ($allNotArrays) {
            return false; // Flat array
        }
        if ($allArrays) {
            // Check if all sub-arrays are not arrays themselves (i.e., exactly two levels)
            foreach ($inputData as $subArray) {
                foreach ($subArray as $subValue) {
                    if (is_array($subValue)) {
                        throw new InvalidConfigurationException('Input data is deeper than two levels.');
                    }
                }
            }
            return true; // Two-deep array
        }

        throw new InvalidConfigurationException('Input data must be a flat array (one field collection) or a two-deep array(many field collections).');
    }

    protected function getFieldCollectionClass(): string
    {
        return '\\Pimcore\\Model\\DataObject\\Fieldcollection\\Data\\' . ucfirst($this->fieldCollectionType);
    }

    /**
     * @param string $inputType
     * @param int|null $index
     *
     * @return string
     *
     * @throws InvalidConfigurationException
     */
    public function evaluateReturnType(string $inputType, int $index = null): string
    {
        return "Field Collection";
    }

    /**
     * @param mixed $inputData
     *
     * @return mixed
     */
    public function generateResultPreview($fieldCollection)
    {
        if (!$this->fieldIndexMappings) {
            return '';
        }

        if (!$fieldCollection instanceof Fieldcollection) {
            return '';
        }

        $itemsPreview = [];
        foreach ($fieldCollection as $fcItem) {
            $fields = [];
            foreach ($this->fieldIndexMappings as $index => $fieldName) {
                $getter = 'get' . ucfirst($fieldName);
                $value = method_exists($fcItem, $getter) ? $fcItem->$getter() : null;
                $fields[] = $fieldName . ': ' . (is_scalar($value) || $value === null ? var_export($value, true) : '[complex]');
            }
            $itemsPreview[] = '{ ' . implode(', ', $fields) . ' }';
        }

        return '[ ' . implode(' | ', $itemsPreview) . ' ]';
    }
}
