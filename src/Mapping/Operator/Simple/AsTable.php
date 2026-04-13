<?php

namespace TorqIT\DataImporterExtensionsBundle\Mapping\Operator\Simple;

use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Bundle\DataImporterBundle\Mapping\Operator\AbstractOperator;
use Pimcore\Bundle\DataImporterBundle\Mapping\Type\TransformationDataTypeService;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'pimcore.datahub.data_importer.operator', attributes: ['type' => 'asTable'])]
class AsTable extends AbstractOperator
{
    public const TABLE_TYPE = 'table';

    protected string $columnDelimiter = ',';
    protected string $rowDelimiter = '|';

    public function setSettings(array $settings): void
    {
        $this->columnDelimiter = $settings['columnDelimiter'] ?? ',';
        $this->rowDelimiter = $settings['rowDelimiter'] ?? '|';
    }

    /**
     * Transform input data into a 2D array suitable for Pimcore table fields.
     *
     * Accepts:
     *   - A string: splits by rowDelimiter then columnDelimiter
     *   - A 1D array: each element is split by columnDelimiter
     *   - A 2D array: pass-through
     */
    public function process($inputData, bool $dryRun = false)
    {
        if (empty($inputData)) {
            return [];
        }

        if (is_string($inputData)) {
            return $this->parseString($inputData);
        }

        if (is_array($inputData)) {
            // Already a 2D array
            if (!empty($inputData) && is_array(reset($inputData))) {
                return array_values(array_map('array_values', $inputData));
            }

            // 1D array: split each element by column delimiter
            return array_values(array_map(function ($row) {
                if (is_string($row) && !empty($this->columnDelimiter)) {
                    return explode($this->columnDelimiter, $row);
                }
                return [$row];
            }, $inputData));
        }

        return [[$inputData]];
    }

    protected function parseString(string $input): array
    {
        $rows = !empty($this->rowDelimiter) ? explode($this->rowDelimiter, $input) : [$input];

        return array_values(array_map(function ($row) {
            if (!empty($this->columnDelimiter)) {
                return explode($this->columnDelimiter, trim($row));
            }
            return [trim($row)];
        }, $rows));
    }

    public function evaluateReturnType(string $inputType, ?int $index = null): string
    {
        $allowedTypes = [
            TransformationDataTypeService::DEFAULT_TYPE,
            TransformationDataTypeService::DEFAULT_ARRAY,
        ];

        if (!in_array($inputType, $allowedTypes)) {
            throw new InvalidConfigurationException(
                sprintf("Unsupported input type '%s' for AsTable operator at transformation position %s", $inputType, $index)
            );
        }

        return self::TABLE_TYPE;
    }

    public function generateResultPreview($inputData)
    {
        $result = $this->process($inputData);
        if (empty($result)) {
            return 'empty table';
        }

        $rows = [];
        foreach ($result as $i => $row) {
            if ($i >= 3) {
                $rows[] = '... (' . count($result) . ' rows total)';
                break;
            }
            $rows[] = '[' . implode(' | ', $row) . ']';
        }

        return implode("\n", $rows);
    }
}
