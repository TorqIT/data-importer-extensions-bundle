<?php

namespace TorqIT\DataImporterExtensionsBundle\DataSource\DataLoader\Xlsx;

use OpenSpout\Common\Entity\Cell\FormulaCell;
use OpenSpout\Reader\XLSX\Reader;

/**
 * Streams rows one by one so large files never have to be loaded into memory at once.
 */

class SpoutXlsxDataLoader implements XlsxDataLoaderInterface
{
    /**
     * @param string $file
     *
     * @param string $sheet
     *
     * @return iterable<array>
     */
    public function getRows(string $file, string $sheet): iterable{

        $reader = new Reader();
        $reader->open($file);

        try {
            foreach($reader->getSheetIterator() as $currentSheet){
                if($currentSheet->getName() != $sheet){
                    continue;
                }

                foreach($currentSheet->getRowIterator() as $row){
                    $cells = $row->getCells();
                    $dataRow = [];
                    foreach ($cells as $cell) {
                        if($cell instanceof FormulaCell){
                            $dataRow[] = $cell->getComputedValue();
                        }else{
                            $dataRow[] = $cell->getValue();
                        }
                    }
                    yield $dataRow;
                }
            }
        } finally {
            $reader->close();
        }
    }

}
