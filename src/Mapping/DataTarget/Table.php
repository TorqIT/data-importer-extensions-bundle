<?php

namespace TorqIT\DataImporterExtensionsBundle\Mapping\DataTarget;

use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Bundle\DataImporterBundle\Mapping\DataTarget\DataTargetInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\Element\ElementInterface;

class Table implements DataTargetInterface
{
    protected string $fieldName = '';
    protected bool $writeIfSourceIsEmpty = true;
    protected bool $writeIfTargetIsNotEmpty = true;

    public function setSettings(array $settings): void
    {
        if (empty($settings['fieldName'])) {
            throw new InvalidConfigurationException('Empty field name.');
        }

        $this->fieldName = $settings['fieldName'];
        $this->writeIfSourceIsEmpty = $settings['writeIfSourceIsEmpty'] ?? true;
        $this->writeIfTargetIsNotEmpty = $settings['writeIfTargetIsNotEmpty'] ?? true;
    }

    public function assignData(ElementInterface $element, $data): void
    {
        if (!$element instanceof DataObject\Concrete) {
            throw new InvalidConfigurationException('Element must be a DataObject for table target.');
        }

        $setterParts = explode('.', $this->fieldName);

        if (count($setterParts) === 1) {
            $getter = 'get' . ucfirst($this->fieldName);
            if (!$this->checkAssignData($data, $element, $getter)) {
                return;
            }
            $this->doAssignData($element, $this->fieldName, $data);
        } elseif (count($setterParts) === 3) {
            // brick attribute
            $brickContainerGetter = 'get' . ucfirst($setterParts[0]);
            $brickContainer = $element->$brickContainerGetter();

            $brickGetter = 'get' . ucfirst($setterParts[1]);
            $brick = $brickContainer->$brickGetter();

            if (empty($brick)) {
                $brickClassName = '\\Pimcore\\Model\\DataObject\\Objectbrick\\Data\\' . ucfirst($setterParts[1]);
                $brick = new $brickClassName($element);
                $brickSetter = 'set' . ucfirst($setterParts[1]);
                $brickContainer->$brickSetter($brick);
            }

            $getter = 'get' . ucfirst($setterParts[2]);
            if (!$this->checkAssignData($data, $brick, $getter)) {
                return;
            }
            $this->doAssignData($brick, $setterParts[2], $data);
        } else {
            throw new InvalidConfigurationException('Invalid number of setter parts for ' . $this->fieldName);
        }
    }

    protected function doAssignData(object $valueContainer, string $fieldName, $data): void
    {
        // Ensure data is a 2D array (table format)
        $tableData = $this->normalizeTableData($data);

        $setter = 'set' . ucfirst($fieldName);
        $valueContainer->$setter($tableData);
    }

    /**
     * Normalize input data into a 2D array suitable for Pimcore's table field.
     * Accepts:
     *   - A 2D array (pass-through)
     *   - A 1D array (wraps each element as a single-column row)
     *   - A string (wraps as single cell)
     */
    protected function normalizeTableData($data): array
    {
        if (!is_array($data)) {
            return [[$data]];
        }

        // Check if it's already a 2D array
        if (!empty($data) && is_array(reset($data))) {
            // Re-index to sequential numeric keys for Pimcore table field
            return array_values(array_map('array_values', $data));
        }

        // 1D array: each element becomes a row with one column
        return array_map(function ($item) {
            return [$item];
        }, array_values($data));
    }

    protected function checkAssignData($newData, object $valueContainer, string $getter): bool
    {
        if ($this->writeIfTargetIsNotEmpty && $this->writeIfSourceIsEmpty) {
            return true;
        }

        $hideUnpublished = DataObject::getHideUnpublished();
        DataObject::setHideUnpublished(false);
        $currentData = $valueContainer->$getter();
        DataObject::setHideUnpublished($hideUnpublished);

        $currentDataIsEmpty = empty($currentData);
        $newDataIsEmpty = empty($newData);

        if (!$this->writeIfTargetIsNotEmpty && !$currentDataIsEmpty) {
            return false;
        }

        if (!$this->writeIfSourceIsEmpty && $newDataIsEmpty) {
            return false;
        }

        return true;
    }
}
