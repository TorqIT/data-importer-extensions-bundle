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

        $data=array();
        $reader = new Reader();
        $reader->open($file);

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
                $data[]=$dataRow;
            }
        }

        $reader->close();

        return $data;
    }
    
}
