<?php

namespace TorqIT\DataImporterExtensionsBundle\DataSource\DataLoader\Xlsx;

use OpenSpout\Common\Entity\Cell\FormulaCell;
use OpenSpout\Reader\XLSX\Reader;

/**
 * PHPOffice uses significantly more memory but is faster. Use this implementation when you have lots of memory and want speed.
 */

class SpoutXlsxDataLoader implements XlsxDataLoaderInterface
{
    /**
     * @param string $file
     *
     * @param string $sheet
     *
     * @return array
     */
    public function getRows(string $file, string $sheet): array{
        return iterator_to_array($this->iterateRows($file, $sheet), false);
    }

    public function iterateRows(string $file, string $sheet): iterable
    {
        $reader = new Reader();
        $reader->open($file);

        try {
            foreach ($reader->getSheetIterator() as $currentSheet) {
                if ($currentSheet->getName() != $sheet) {
                    continue;
                }

                foreach ($currentSheet->getRowIterator() as $row) {
                    $dataRow = [];
                    foreach ($row->getCells() as $cell) {
                        $dataRow[] = $cell instanceof FormulaCell ? $cell->getComputedValue() : $cell->getValue();
                    }
                    yield $dataRow;
                }
            }
        } finally {
            $reader->close();
        }
    }

}
