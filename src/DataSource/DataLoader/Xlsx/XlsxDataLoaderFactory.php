<?php

namespace TorqIT\DataImporterExtensionsBundle\DataSource\DataLoader\Xlsx;

class XlsxDataLoaderFactory
{
    public static function getExcelDataLoader(): XlsxDataLoaderInterface
    {
        return new BoxXlsxDataLoader();
    }
}