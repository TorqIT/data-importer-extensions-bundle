<?php declare(strict_types=1);

namespace TorqIT\DataImporterExtensionsBundle\DataSource\DataLoader\Xlsx;

use DateInterval;
use DateTimeInterface;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Cell\EmptyCell;
use OpenSpout\Common\Entity\Cell\ErrorCell;
use OpenSpout\Common\Entity\Cell\FormulaCell;
use OpenSpout\Reader\XLSX\Reader;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

/**
 * Streams rows one by one so large files never have to be loaded into memory at once.
 * Cell values are formatted like PhpSpreadsheet's toArray() so an import does not depend
 * on which loader read the workbook.
 */
class SpoutXlsxDataLoader implements XlsxDataLoaderInterface
{
    /**
     * @return iterable<array<int, ?string>>
     */
    public function getRows(string $file, string $sheet): iterable
    {
        $reader = new Reader();
        $reader->open($file);

        try {
            foreach ($reader->getSheetIterator() as $currentSheet) {
                if ($currentSheet->getName() !== $sheet) {
                    continue;
                }

                foreach ($currentSheet->getRowIterator() as $row) {
                    $dataRow = [];
                    foreach ($row->getCells() as $cell) {
                        $dataRow[] = $this->formatCellValue($cell);
                    }
                    yield $dataRow;
                }
            }
        } finally {
            $reader->close();
        }
    }

    private function formatCellValue(Cell $cell): ?string
    {
        if ($cell instanceof EmptyCell) {
            return null;
        }

        if ($cell instanceof ErrorCell) {
            return $cell->getRawValue();
        }

        // Formulas cannot be recalculated while streaming, only the result cached in the file is available.
        $value = $cell instanceof FormulaCell ? $cell->getComputedValue() : $cell->getValue();

        if ($value === null) {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            $value = ExcelDate::dateTimeToExcel($value);
        } elseif ($value instanceof DateInterval) {
            $value = $this->dateIntervalToExcelSerial($value);
        }

        return NumberFormat::toFormattedString($value, NumberFormat::FORMAT_GENERAL);
    }

    private function dateIntervalToExcelSerial(DateInterval $interval): float
    {
        $seconds = ($interval->d * 86400) + ($interval->h * 3600) + ($interval->i * 60) + $interval->s;
        $serial = $seconds / 86400;

        return $interval->invert === 1 ? -$serial : $serial;
    }
}
