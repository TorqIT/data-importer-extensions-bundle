<?php

declare(strict_types=1);

namespace TorqIT\DataImporterExtensionsBundle\Mapping\Operator\Factory;

use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Bundle\DataImporterBundle\Mapping\Operator\Factory\QuantityValueArray as BaseQuantityValueArray;
use Pimcore\Bundle\DataImporterBundle\Mapping\Type\TransformationDataTypeService;
use Pimcore\Model\DataObject\Data\QuantityValue;
use Pimcore\Model\DataObject\QuantityValue\Unit;

class QuantityValueArray extends BaseQuantityValueArray
{
    protected string $unitSource = 'id';
    protected ?string $staticUnitId = null;
    protected bool $unitNullIfNoValue = false;

    public function setSettings(array $settings): void
    {
        $this->unitSource = $settings['unitSourceSelect'] ?? 'id';
        $this->staticUnitId = $settings['staticUnitSelect'] ?? null;
        $this->unitNullIfNoValue = (bool)($settings['unitNullIfNoValueCheckbox'] ?? false);
    }

    public function process($inputData, bool $dryRun = false)
    {
        if (!is_array($inputData)) {
            return [];
        }

        $result = [];

        foreach ($inputData as $key => $data) {
            $value = null;
            $unitId = null;

            switch ($this->unitSource) {
                case 'id':
                    $value = $data[0] ?? null;
                    $unitId = $data[1] ?? null;
                    break;

                case 'abbr':
                    $value = $data[0] ?? null;
                    if (isset($data[1])) {
                        $unit = Unit::getByAbbreviation($data[1]);
                        if ($unit instanceof Unit) {
                            $unitId = $unit->getId();
                        }
                    }
                    break;

                case 'static':
                    $value = is_array($data) ? ($data[0] ?? null) : $data;
                    $unitId = $this->staticUnitId;
                    break;
            }

            if (($value === null || $value === '') && $this->unitNullIfNoValue) {
                $unitId = null;
            }

            if (($value === null || $value === '') && $unitId === null) {
                $result[$key] = null;
                continue;
            }

            $result[$key] = new QuantityValue(
                $value === null ? null : floatval($value), $unitId
            );
        }

        return $result;
    }

    public function evaluateReturnType(string $inputType, ?int $index = null): string
    {
        if ($inputType !== TransformationDataTypeService::DEFAULT_ARRAY) {
            throw new InvalidConfigurationException(
                sprintf(
                    "Unsupported input type '%s' for quantity value array operator at transformation position %s",
                    $inputType,
                    $index
                )
            );
        }

        return TransformationDataTypeService::QUANTITY_VALUE_ARRAY;
    }
}
