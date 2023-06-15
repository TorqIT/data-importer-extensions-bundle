<?php

namespace TorqIT\DataImporterExtensionsBundle\DataSource\DataLoader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;


/**
 * PHPOffice uses significantly more memory but is faster. Use this implementation when you have lots of memory and want speed.
 */

class BoxXlsxDataLoader implements XlsxDataLoaderInterface
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
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open($file);

        foreach($reader->getSheetIterator() as $currentSheet){
            if($currentSheet->getName() != $sheet){
                continue;
            }

            foreach($currentSheet->getRowIterator() as $row){
                $cells = $row->getCells();
                $dataRow = [];
                foreach ($cells as $cell) {
                    $dataRow[] = $cell->getValue();
                }
                $data[]=$dataRow;
            }
        }

        $reader->close();

        return $data;
    }
    
}