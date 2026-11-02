<?php

namespace TorqIT\DataImporterExtensionsBundle\Override;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Pimcore\Bundle\DataImporterBundle\DataSource\Interpreter\AbstractInterpreter;
use Pimcore\Bundle\DataImporterBundle\Preview\Model\PreviewData;
use TorqIT\DataImporterExtensionsBundle\DataSource\Interpreter\PreviewRowsReadFilter;

// Copy of Pimcore\Bundle\DataImporterBundle\DataSource\Interpreter\XlsxFileInterpreter
class CustomXlsxFileInterpreter extends AbstractInterpreter
{
    protected bool $skipFirstRow;
    protected string $sheetName;

    protected function doInterpretFileAndCallProcessRow(string $path): void
    {
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $spreadSheet = $reader->load($path);

        $spreadSheet->setActiveSheetIndexByName($this->sheetName);

        $data = $spreadSheet->getActiveSheet()->toArray();

        if ($this->skipFirstRow) {
            array_shift($data);
        }

        foreach ($data as $rowData) {
            $this->processImportRow($rowData);
        }
    }

    public function fileValid(string $path, bool $originalFilename = false): bool
    {
        $reader = IOFactory::createReaderForFile($path);

        return $reader->canRead($path);
    }

    public function previewData(string $path, int $recordNumber = 0, array $mappedColumns = []): PreviewData
    {
        $previewData = [];
        $columns = [];
        $readRecordNumber = 0;

        if ($this->fileValid($path)) {
            $totalRows = $this->getTotalRows($path);
            $headerRowNumber = $this->skipFirstRow ? 1 : 0;
            $totalDataRows = max(0, $totalRows - $headerRowNumber);

            if ($totalDataRows > 0) {
                $targetRowNumber = $headerRowNumber + 1 + $recordNumber;
                if ($targetRowNumber > $totalRows) {
                    $targetRowNumber = $totalRows;
                    $readRecordNumber = $totalDataRows - 1;
                } else {
                    $readRecordNumber = $recordNumber;
                }

                // Only load the header row and the requested data row instead of the
                // whole workbook - large files would otherwise exhaust the memory limit.
                $reader = IOFactory::createReaderForFile($path);
                $reader->setReadDataOnly(true);
                $reader->setLoadSheetsOnly($this->sheetName);
                $reader->setReadFilter(new PreviewRowsReadFilter(array_filter([$headerRowNumber, $targetRowNumber])));
                $spreadSheet = $reader->load($path);

                $spreadSheet->setActiveSheetIndexByName($this->sheetName);
                $sheet = $spreadSheet->getActiveSheet();
                $highestColumn = $sheet->getHighestColumn();

                if ($this->skipFirstRow) {
                    $firstRow = $this->readPreviewRow($sheet, $headerRowNumber, $highestColumn);
                    foreach ($firstRow as $index => $columnHeader) {
                        $columns[$index] = trim((string)$columnHeader) . " [$index]";
                    }
                }

                $previewDataRow = $this->readPreviewRow($sheet, $targetRowNumber, $highestColumn);

                foreach ($previewDataRow as $index => $columnData) {
                    $previewData[$index] = $columnData;
                }

                if (empty($columns)) {
                    $columns = array_keys($previewData);
                }
            }
        }

        return new PreviewData($columns, $previewData, $readRecordNumber, $mappedColumns);
    }

    protected function readPreviewRow(Worksheet $sheet, int $rowNumber, string $lastColumnLetter): array
    {
        $lastColumnIndex = Coordinate::columnIndexFromString($lastColumnLetter);
        $rowData = [];

        for ($column = 1; $column <= $lastColumnIndex; $column++) {
            $rowData[] = $this->getPreviewCellValue($sheet->getCell([$column, $rowNumber]));
        }

        return $rowData;
    }

    /**
     * Formula cells prefer the result cached in the file: only parts of the
     * workbook are loaded for previews, so recalculating formulas with
     * cross-row or cross-sheet references would silently produce wrong
     * values ("#REF!", zeros).
     */
    protected function getPreviewCellValue(Cell $cell): mixed
    {
        if ($cell->getDataType() === DataType::TYPE_FORMULA) {
            $cachedValue = $cell->getOldCalculatedValue();
            if ($cachedValue !== null) {
                return $cachedValue;
            }
        }

        try {
            $value = $cell->getCalculatedValue();
        } catch (\Throwable $e) {
            return null;
        }

        if ($value instanceof RichText) {
            return $value->getPlainText();
        }

        return $value;
    }

    protected function getTotalRows(string $path): int
    {
        $reader = IOFactory::createReaderForFile($path);

        foreach ($reader->listWorksheetInfo($path) as $worksheetInfo) {
            if (($worksheetInfo['worksheetName'] ?? null) === $this->sheetName) {
                return (int)($worksheetInfo['totalRows'] ?? 0);
            }
        }

        return 0;
    }

    public function setSettings(array $settings): void
    {
        $this->skipFirstRow = $settings['skipFirstRow'] ?? false;
        $this->sheetName = $settings['sheetName'] ?? 'Sheet1';
    }
}