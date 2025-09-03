<?php

namespace TorqIT\DataImporterExtensionsBundle\Mapping\Operator\Factory;

use Pimcore\Bundle\DataImporterBundle\Mapping\Operator\AbstractOperator;
use Pimcore\Model\DataObject\Data\QuantityValueRange;

class QuantityValueRangeArray extends AbstractOperator
{
    public function process($inputData, bool $dryRun = false)
    {
        if (!is_array($inputData)) {
            return [];
        }

        $result = [];
        foreach ($inputData as $key => $data) {
            $minimum = $data[0] ?? null;
            $maximum = $data[1] ?? null;
            $unit = $data[2] ?? null;
            if ($minimum && $maximum && $unit) {
                $result[$key] = new QuantityValueRange($minimum, $maximum, $unit);
            }
        }
        return $result;
    }

    public function evaluateReturnType(string $inputType, ?int $index = null): string
    {
        return 'array';
    }

    public function generateResultPreview($inputData)
    {
        if (is_array($inputData)) {
            $preview = [];

            foreach ($inputData as $key => $data) {
                if ($data instanceof QuantityValueRange) {
                    $preview[$key] = 'QuantityValueRange: ' . $data->getMinimum() . ' - ' . $data->getMaximum(
                        ) . ' ' . ($data->getUnit() ? $data->getUnit()->getAbbreviation() : '');
                } else {
                    $preview[$key] = $data;
                }
            }

            return $preview;
        }

        return $inputData;
    }
}