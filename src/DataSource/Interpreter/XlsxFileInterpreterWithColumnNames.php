<?php

/*
 * This class allows one definition of previewData() to be shared by all extending XLS Interpreters.
 */

namespace TorqIT\DataImporterExtensionsBundle\DataSource\Interpreter;

use Pimcore\Bundle\DataImporterBundle\Preview\Model\PreviewData;
use Pimcore\Bundle\DataImporterBundle\DataSource\Interpreter\XlsxFileInterpreter;
use TorqIT\DataImporterExtensionsBundle\DataSource\DataLoader\Xlsx\XlsxDataLoaderFactory;

abstract class XlsxFileInterpreterWithColumnNames extends XlsxFileInterpreter
{
    /**
     * @var bool
     *
     * If true, index columns by the header name in the first row, instead of using numbered index.
     */
    protected bool $saveHeaderName = false;

    /**
     * @var int
     *
     * The row number containing the headers (1-indexed). Rows before this are skipped.
     * Default is 1 (first row).
     */
    protected int $headerRow = 1;

    /* Overriding parent method to allow for using column header names as keys and configurable header row.
     * Uses the same OpenSpout loader as doInterpretFileAndCallProcessRow to ensure consistent empty-row handling. */
    public function previewData(string $path, int $recordNumber = 0, array $mappedColumns = []): PreviewData
    {
        $previewData = [];
        $columns = [];
        $readRecordNumber = 0;

        if ($this->fileValid($path)) {
            $excelLoader = XlsxDataLoaderFactory::getExcelDataLoader();
            $data = $excelLoader->getRows($path, $this->sheetName);

            // Header row is 1-indexed; array is 0-indexed
            $headerRowIndex = $this->headerRow - 1;
            $headerRowData = $data[$headerRowIndex] ?? [];

            // Build column names from header row
            if ($this->saveHeaderName) {
                foreach ($headerRowData as $columnHeader) {
                    $columns[$columnHeader] = trim((string)$columnHeader);
                }
            } else {
                foreach ($headerRowData as $index => $columnHeader) {
                    $columns[$index] = trim((string)$columnHeader) . " [$index]";
                }
            }

            // Data rows start after the header row (same slice as doInterpretFileAndCallProcessRow)
            $dataRows = array_slice($data, $this->headerRow);
            $totalDataRows = count($dataRows);

            if ($totalDataRows > 0) {
                if ($recordNumber >= $totalDataRows) {
                    $recordNumber = $totalDataRows - 1;
                }
                $readRecordNumber = $recordNumber;

                $previewDataRow = $dataRows[$recordNumber];

                // Combine header names with data values if using header names
                if ($this->saveHeaderName && !empty($headerRowData)) {
                    if (count($headerRowData) > count($previewDataRow)) {
                        $previewDataRow = array_pad($previewDataRow, count($headerRowData), null);
                    } elseif (count($headerRowData) < count($previewDataRow)) {
                        $previewDataRow = array_slice($previewDataRow, 0, count($headerRowData));
                    }
                    $previewDataRow = array_combine($headerRowData, $previewDataRow);
                }

                $previewData = $previewDataRow;
            }

            if (empty($columns)) {
                $columns = array_keys($previewData);
            }
        }

        return new PreviewData($columns, $previewData, $readRecordNumber, $mappedColumns);
    }

    public function setSettings(array $settings): void
    {
        parent::setSettings($settings);

        $this->saveHeaderName = $settings['saveHeaderName'] ?? false;
        $this->headerRow = (int)($settings['headerRow'] ?? 1);
    }
}
