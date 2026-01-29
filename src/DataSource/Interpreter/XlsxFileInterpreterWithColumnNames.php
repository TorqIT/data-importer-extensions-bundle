<?php


/*
 * This class allows one definition of previewData() to be shared by all extending XLS Interpreters.
 */

namespace TorqIT\DataImporterExtensionsBundle\DataSource\Interpreter;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Pimcore\Bundle\DataImporterBundle\Preview\Model\PreviewData;
use Pimcore\Bundle\DataImporterBundle\DataSource\Interpreter\XlsxFileInterpreter;

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

    /**
     * Get cell value with graceful formula error handling.
     *
     * For cells with formulas that can't be calculated (e.g., Excel-specific functions
     * like _xlfn._LONGTEXT), this will return the cached value if available,
     * or null if no cached value exists.
     */
    private function getCellValueSafe(Cell $cell): mixed
    {
        try {
            return $cell->getCalculatedValue();
        } catch (\Throwable $e) {
            // Formula calculation failed - try to get the cached value
            // This is the value that Excel calculated and stored in the file
            $oldValue = $cell->getOldCalculatedValue();
            if ($oldValue !== null) {
                return $oldValue;
            }

            // No cached value available, return null
            return null;
        }
    }

    /**
     * Read a row from the worksheet with safe formula handling.
     */
    private function readRowSafe(Worksheet $sheet, int $rowNumber): array
    {
        $rowData = [];
        $highestColumn = $sheet->getHighestColumn($rowNumber);
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $columnLetter = Coordinate::stringFromColumnIndex($col);
            $cell = $sheet->getCell($columnLetter . $rowNumber);
            $rowData[] = $this->getCellValueSafe($cell);
        }

        return $rowData;
    }

    /* Overriding parent method to allow for using column header names as keys and configurable header row */
    public function previewData(string $path, int $recordNumber = 0, array $mappedColumns = []): PreviewData
    {
        $previewData = [];
        $columns = [];
        $readRecordNumber = 0;
        $headerRowData = null;

        if ($this->fileValid($path)) {
            $reader = IOFactory::createReaderForFile($path);
            $spreadSheet = $reader->load($path);
            $spreadSheet->setActiveSheetIndexByName($this->sheetName);
            $sheet = $spreadSheet->getActiveSheet();

            // Get total row count
            $highestRow = $sheet->getHighestRow();

            // Read header row with safe formula handling
            $headerRowData = $this->readRowSafe($sheet, $this->headerRow);

            // Build column names from header row
            if ($this->saveHeaderName) {
                foreach ($headerRowData as $index => $columnHeader) {
                    $columns[$columnHeader] = trim((string)$columnHeader);
                }
            } else {
                foreach ($headerRowData as $index => $columnHeader) {
                    $columns[$index] = trim((string)$columnHeader) . " [$index]";
                }
            }

            // Calculate which row to preview (data starts after header row)
            $dataStartRow = $this->headerRow + 1;
            $totalDataRows = $highestRow - $this->headerRow;

            // Determine the actual row number to read
            $targetRow = $dataStartRow + $recordNumber;
            if ($targetRow > $highestRow) {
                $targetRow = $highestRow;
                $readRecordNumber = max(0, $totalDataRows - 1);
            } else {
                $readRecordNumber = $recordNumber;
            }

            // Read the preview row with safe formula handling
            $previewDataRow = $this->readRowSafe($sheet, $targetRow);

            // Combine header names with data values if using header names
            if ($this->saveHeaderName && !empty($headerRowData)) {
                // Ensure arrays are same length
                if (count($headerRowData) > count($previewDataRow)) {
                    $previewDataRow = array_pad($previewDataRow, count($headerRowData), null);
                } elseif (count($headerRowData) < count($previewDataRow)) {
                    $previewDataRow = array_slice($previewDataRow, 0, count($headerRowData));
                }
                $previewDataRow = array_combine($headerRowData, $previewDataRow);
            }

            foreach ($previewDataRow as $index => $columnData) {
                $previewData[$index] = $columnData;
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
