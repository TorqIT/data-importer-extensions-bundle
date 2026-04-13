<?php

namespace TorqIT\DataImporterExtensionsBundle\Mapping\Operator\Factory;

use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Bundle\DataImporterBundle\Mapping\Operator\AbstractOperator;
use Pimcore\Bundle\DataImporterBundle\Mapping\Type\TransformationDataTypeService;
use Pimcore\Model\DataObject\Data\QuantityValueRange;
use Pimcore\Model\DataObject\QuantityValue\Unit;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'pimcore.datahub.data_importer.operator', attributes: ['type' => 'quantityValueRangeArray'])]
class QuantityValueRangeArray extends AbstractOperator
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
            $minimum = $data[0] ?? null;
            $maximum = $data[1] ?? null;
            $unitId = null;

            switch ($this->unitSource) {
                case 'id':
                    $unitId = $data[2] ?? null;
                    break;

                case 'abbr':
                    if (isset($data[2])) {
                        $unit = Unit::getByAbbreviation($data[2]);
                        if ($unit instanceof Unit) {
                            $unitId = $unit->getId();
                        }
                    }
                    break;

                case 'static':
                    $unitId = $this->staticUnitId;
                    break;
            }

            if (($minimum === null && $maximum === null) && $this->unitNullIfNoValue) {
                $unitId = null;
            }

            if ($minimum !== null && $maximum !== null && $unitId !== null) {
                $result[$key] = new QuantityValueRange(floatval($minimum), floatval($maximum), $unitId);
            }
        }

        return $result;
    }

    public function evaluateReturnType(string $inputType, ?int $index = null): string
    {
        if ($inputType !== TransformationDataTypeService::DEFAULT_ARRAY) {
            throw new InvalidConfigurationException(
                sprintf(
                    "Unsupported input type '%s' for quantity value range array operator at transformation position %s",
                    $inputType,
                    $index
                )
            );
        }

        return 'array';
    }

    public function generateResultPreview($inputData)
    {
        if (!is_array($inputData)) {
            return $inputData;
        }

        $preview = [];

        foreach ($inputData as $key => $data) {
            if ($data instanceof QuantityValueRange) {
                $preview[$key] = 'QuantityValueRange: ' .
                    $data->getMinimum() .
                    ' - ' .
                    $data->getMaximum() .
                    ' ' .
                    ($data->getUnit() ? $data->getUnit()->getAbbreviation() : '');
            } else {
                $preview[$key] = $data;
            }
        }

        return $preview;
    }
}