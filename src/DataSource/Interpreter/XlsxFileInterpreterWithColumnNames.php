<?php

declare(strict_types=1);

/*
 * This class allows one definition of previewData() to be shared by all extending XLS Interpreters.
 */

namespace TorqIT\DataImporterExtensionsBundle\DataSource\Interpreter;

use Pimcore\Bundle\DataImporterBundle\Preview\Model\PreviewData;
use TorqIT\DataImporterExtensionsBundle\DataSource\DataLoader\Xlsx\XlsxDataLoaderFactory;
use TorqIT\DataImporterExtensionsBundle\Override\CustomXlsxFileInterpreter;

abstract class XlsxFileInterpreterWithColumnNames extends CustomXlsxFileInterpreter
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
     * The position of the header row (1-indexed) among the non-empty rows of the sheet,
     * because OpenSpout skips empty rows. Rows before it are skipped. Default is 1.
     */
    protected int $headerRow = 1;

    /*
     * Reads rows through the same OpenSpout loader as doInterpretFileAndCallProcessRow so preview
     * record N is the same row the import processes as record N (both skip empty rows).
     * Rows are streamed and reading stops at the requested record to keep memory flat on large files.
     */
    public function previewData(string $path, int $recordNumber = 0, array $mappedColumns = []): PreviewData
    {
        $previewData = [];
        $columns = [];
        $readRecordNumber = 0;

        if (!$this->fileValid($path)) {
            return new PreviewData($columns, $previewData, $readRecordNumber, $mappedColumns);
        }

        $headerRowIndex = $this->headerRow - 1;
        $headerRowData = [];
        $previewDataRow = null;
        $dataRowNumber = -1;

        $rows = XlsxDataLoaderFactory::getExcelDataLoader()->iterateRows($path, $this->sheetName);
        foreach ($rows as $rowIndex => $row) {
            if ($rowIndex < $headerRowIndex) {
                continue;
            }

            if ($rowIndex === $headerRowIndex) {
                $headerRowData = $row;
                continue;
            }

            $dataRowNumber++;
            $previewDataRow = $row;

            if ($dataRowNumber >= $recordNumber) {
                break;
            }
        }

        if ($this->saveHeaderName) {
            foreach ($headerRowData as $columnHeader) {
                $columns[$columnHeader] = trim((string)$columnHeader);
            }
        } else {
            foreach ($headerRowData as $index => $columnHeader) {
                $columns[$index] = trim((string)$columnHeader) . " [$index]";
            }
        }

        if ($previewDataRow !== null) {
            $readRecordNumber = $dataRowNumber;
            $previewData = $this->combineWithHeader($headerRowData, $previewDataRow);
        }

        if (empty($columns)) {
            $columns = array_keys($previewData);
        }

        return new PreviewData($columns, $previewData, $readRecordNumber, $mappedColumns);
    }

    private function combineWithHeader(array $headerRowData, array $rowData): array
    {
        if (!$this->saveHeaderName || empty($headerRowData)) {
            return $rowData;
        }

        $headerCount = count($headerRowData);
        if ($headerCount > count($rowData)) {
            $rowData = array_pad($rowData, $headerCount, null);
        } elseif ($headerCount < count($rowData)) {
            $rowData = array_slice($rowData, 0, $headerCount);
        }

        return array_combine($headerRowData, $rowData);
    }

    public function setSettings(array $settings): void
    {
        parent::setSettings($settings);

        $this->saveHeaderName = $settings['saveHeaderName'] ?? false;
        $this->headerRow = max(1, (int)($settings['headerRow'] ?? 1));
    }
}
