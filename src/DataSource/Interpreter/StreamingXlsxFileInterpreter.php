<?php declare(strict_types=1);

namespace TorqIT\DataImporterExtensionsBundle\DataSource\Interpreter;

use Generator;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Pimcore\Bundle\DataImporterBundle\DataSource\Interpreter\AbstractInterpreter;
use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Bundle\DataImporterBundle\Preview\Model\PreviewData;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use TorqIT\DataImporterExtensionsBundle\DataSource\DataLoader\Xlsx\XlsxDataLoaderFactory;

/**
 * Base XLSX interpreter that streams the sheet row by row through the configured data loader
 * and reads previews through a row filter, so large workbooks never have to be loaded into
 * memory. Supports a configurable header row, header names as column keys, de-duplication on
 * unique columns and an expression-language row filter.
 */
abstract class StreamingXlsxFileInterpreter extends AbstractInterpreter
{
    protected string $sheetName = 'Sheet1';

    /**
     * If true, index columns by the header name in the header row instead of the numeric index.
     */
    protected bool $saveHeaderName = false;

    /**
     * 1-based number of the row containing the headers. Rows up to and including it are not imported.
     */
    protected int $headerRow = 1;

    /**
     * @var string[] column indexes whose combined value identifies a row; later duplicates are skipped
     */
    protected array $uniqueColumns = [];

    protected string $rowFilter = '';

    protected function doInterpretFileAndCallProcessRow(string $path): void
    {
        foreach ($this->streamImportRows($path) as $rowData) {
            $this->processImportRow($rowData);
        }
    }

    /**
     * Yields the data rows of the configured sheet one by one: rows up to and including the
     * header row are skipped, duplicates on the unique columns and rows rejected by the row
     * filter are dropped, and values are keyed by header name when configured. Rows are
     * padded to the sheet width so column indexes stay stable across rows.
     *
     * @return Generator<int, array<int|string, mixed>>
     *
     * @throws InvalidConfigurationException when the configured sheet does not exist
     */
    protected function streamImportRows(string $path): Generator
    {
        $worksheetInfo = $this->getWorksheetInfo($path);

        if ($worksheetInfo === null) {
            throw new InvalidConfigurationException(sprintf('Sheet "%s" not found in file.', $this->sheetName));
        }

        $expressionLanguage = $this->rowFilter !== '' ? new ExpressionLanguage() : null;
        $headerRow = null;
        $uniqueHashes = [];
        $rowNumber = 0;

        foreach (XlsxDataLoaderFactory::getExcelDataLoader()->getRows($path, $this->sheetName) as $rowData) {
            $rowNumber++;
            $rowData = array_pad($rowData, $worksheetInfo['totalColumns'], null);

            if ($rowNumber <= $this->headerRow) {
                if ($rowNumber === $this->headerRow && $this->saveHeaderName) {
                    $headerRow = $rowData;
                }

                continue;
            }

            $hashKey = $this->buildUniqueKey($rowData);
            if ($hashKey !== '' && isset($uniqueHashes[$hashKey])) {
                continue;
            }

            if ($expressionLanguage !== null && !$expressionLanguage->evaluate($this->rowFilter, ['row' => $rowData])) {
                continue;
            }

            if ($headerRow !== null) {
                $rowData = $this->combineWithHeader($headerRow, $rowData);
            }

            yield $rowData;

            $uniqueHashes[$hashKey] = true;
        }
    }

    /**
     * @param array<int, mixed> $rowData
     */
    protected function buildUniqueKey(array $rowData): string
    {
        $hashKey = '';

        foreach ($this->uniqueColumns as $index) {
            $hashKey .= (string)($rowData[$index] ?? '');
        }

        return $hashKey;
    }

    /**
     * Keys the row values by the header names, trimming or padding the row to the header width.
     *
     * @param array<int, mixed> $headerRow
     * @param array<int, mixed> $rowData
     *
     * @return array<int|string, mixed>
     */
    protected function combineWithHeader(array $headerRow, array $rowData): array
    {
        if (count($headerRow) > count($rowData)) {
            $rowData = array_pad($rowData, count($headerRow), null);
        } elseif (count($headerRow) < count($rowData)) {
            $rowData = array_slice($rowData, 0, count($headerRow));
        }

        return array_combine($headerRow, $rowData);
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

        $rows = $this->fileValid($path) ? $this->loadPreviewRows($path, $recordNumber) : null;

        if ($rows !== null) {
            [$headerRowData, $previewData, $readRecordNumber] = $rows;

            $columns = $this->extractColumns($headerRowData);

            if ($this->saveHeaderName) {
                $previewData = $this->combineWithHeader($headerRowData, $previewData);
            }

            if (empty($columns)) {
                $columns = array_keys($previewData);
            }
        }

        return new PreviewData($columns, $previewData, $readRecordNumber, $mappedColumns);
    }

    /**
     * Reads the header row and the requested data row without loading the whole
     * workbook - large files would otherwise exhaust the memory limit. Formulas and
     * their cached values are still loaded for the filtered rows.
     *
     * @return array{0: array<int, mixed>, 1: array<int, mixed>, 2: int}|null null when the sheet holds no data rows
     */
    protected function loadPreviewRows(string $path, int $recordNumber): ?array
    {
        $totalRows = $this->getTotalRows($path);
        $totalDataRows = $totalRows - $this->headerRow;

        if ($totalDataRows < 1) {
            return null;
        }

        $targetRow = $this->headerRow + 1 + $recordNumber;
        $readRecordNumber = $recordNumber;
        if ($recordNumber < 0 || $targetRow > $totalRows) {
            $targetRow = $totalRows;
            $readRecordNumber = $totalDataRows - 1;
        }

        $reader = IOFactory::createReaderForFile($path);
        $reader->setLoadSheetsOnly($this->sheetName);
        $reader->setReadFilter(new PreviewRowsReadFilter([$this->headerRow, $targetRow]));
        $spreadSheet = $reader->load($path);
        $spreadSheet->setActiveSheetIndexByName($this->sheetName);
        $sheet = $spreadSheet->getActiveSheet();

        return [
            $this->readPreviewRow($sheet, $this->headerRow),
            $this->readPreviewRow($sheet, $targetRow),
            $readRecordNumber,
        ];
    }

    /**
     * @param array<int, mixed> $headerRow
     *
     * @return array<int|string, string>
     */
    protected function extractColumns(array $headerRow): array
    {
        $columns = [];

        foreach ($headerRow as $index => $columnHeader) {
            if ($this->saveHeaderName) {
                $columns[$columnHeader] = trim((string)$columnHeader);
            } else {
                $columns[$index] = trim((string)$columnHeader) . " [$index]";
            }
        }

        return $columns;
    }

    /**
     * @return array<int, mixed>
     */
    protected function readPreviewRow(Worksheet $sheet, int $rowNumber): array
    {
        $highestColumnIndex = Coordinate::columnIndexFromString($sheet->getHighestColumn($rowNumber));
        $rowData = [];

        for ($column = 1; $column <= $highestColumnIndex; $column++) {
            $rowData[] = $this->getPreviewCellValue($sheet->getCell([$column, $rowNumber]));
        }

        return $rowData;
    }

    /**
     * Formula cells prefer the result cached in the file: only parts of the
     * workbook are loaded for previews, so recalculating formulas with
     * cross-row or cross-sheet references would silently produce wrong
     * values ("#REF!", zeros). The cached value also covers formulas that
     * cannot be calculated at all (e.g. Excel-specific functions like
     * _xlfn._LONGTEXT).
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
        } catch (\Throwable) {
            return null;
        }

        if ($value instanceof RichText) {
            return $value->getPlainText();
        }

        return $value;
    }

    protected function getTotalRows(string $path): int
    {
        return $this->getWorksheetInfo($path)['totalRows'] ?? 0;
    }

    /**
     * Reads the worksheet dimensions without loading any cells.
     * Returns null when the configured sheet does not exist in the file.
     *
     * @return array{totalRows: int, totalColumns: int}|null
     */
    protected function getWorksheetInfo(string $path): ?array
    {
        $reader = IOFactory::createReaderForFile($path);

        foreach ($reader->listWorksheetInfo($path) as $worksheetInfo) {
            if ($worksheetInfo['worksheetName'] === $this->sheetName) {
                return [
                    'totalRows' => $worksheetInfo['totalRows'],
                    'totalColumns' => $worksheetInfo['totalColumns'],
                ];
            }
        }

        return null;
    }

    public function setSettings(array $settings): void
    {
        $this->sheetName = (string)($settings['sheetName'] ?? 'Sheet1');
        $this->saveHeaderName = (bool)($settings['saveHeaderName'] ?? false);
        $this->headerRow = (int)($settings['headerRow'] ?? 1);
        $this->rowFilter = (string)($settings['rowFilter'] ?? '');

        $uniqueColumns = trim((string)($settings['uniqueColumns'] ?? ''));
        $this->uniqueColumns = $uniqueColumns !== '' ? explode(',', $uniqueColumns) : [];
    }
}
