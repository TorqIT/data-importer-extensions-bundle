<?php

namespace TorqIT\DataImporterExtensionsBundle\DataSource\DataLoader\Xlsx;

class XlsxDataLoaderFactory
{
    public static function getExcelDataLoader(bool $lowMemorySupport): XlsxDataLoaderInterface
    {
        if($lowMemorySupport)
        {
            return new BoxXlsxDataLoader();
        }
        else 
        {
            return new PhpOfficeXlsxDataLoader();
        }
    }
}