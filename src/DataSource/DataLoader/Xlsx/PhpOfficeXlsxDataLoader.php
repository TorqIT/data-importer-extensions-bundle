<?php

namespace TorqIT\DataImporterExtensionsBundle\DataSource\DataLoader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * PHPOffice uses significantly more memory but is faster. Use this implementation when you have lots of memory and want speed.
 */

class PhpOfficeXlsxDataLoader implements XlsxDataLoaderInterface
{
    /**
     * @param string $file
     * 
     * @param string $sheet
     * 
     * @return array
     */
    public function getRows(string $file, string $sheet): array{

        $reader = IOFactory::createReaderForFile($file);
        $reader->setReadDataOnly(true);
        $spreadSheet = $reader->load($file);

        $spreadSheet->setActiveSheetIndexByName($sheet);

        return $spreadSheet->getActiveSheet()->toArray();
    }
    
}