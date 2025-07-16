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

    public function setSettings(array $settings): void
    {
        if (empty($settings['fieldCollectionKey'])) {
            throw new InvalidConfigurationException("Field Collection: 'array' must be provided.");
        }

        $this->fieldCollectionType = $settings['fieldCollectionKey'];
        $this->fieldMappings = $settings['fieldMappings'] ?? [];

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
        $this->generateFieldCollection($inputData);
    }

    /**
     * @param mixed $inputData
     *
     * @return array|false|mixed|null
     */
    private function generateFieldCollection($inputData)
    {
        if (!is_array($inputData)) {
            throw new InvalidConfigurationException("Field Collection expects input data as an array.");
        }

        $className = $this->getFieldCollectionClass();
        /** @var AbstractData $fcItem */
        $fcItem = new $className();

        $fieldIndex = 0;
        foreach ($this->fieldMappings as $field => $inputKey) {
            $setter = 'set' . ucfirst($field);
            if (method_exists($fcItem, $setter) && $field !== 'Location') {
                $fcItem->$setter($inputData[$fieldIndex]);
                $this->fieldMappings[$field] = $inputData[$fieldIndex];
            }
            $fieldIndex++;
        }

        $collection = new Fieldcollection();
        $collection->add($fcItem);

        return $collection;
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
    public function generateResultPreview($inputData)
    {
        if (!$this->fieldMappings) {
            return '';
        }

        $pairs = [];

        foreach ($this->fieldMappings as $field => $value) {
            $pairs[] = "'{$field}' => {$value}";
        }

        $stringResult = '[ ' . implode(' | ', $pairs) . ' ]';

        return $stringResult;
    }
}
