<?php

namespace TorqIT\DataImporterExtensionsBundle\DataSource\DataLoader\Xlsx;


interface XlsxDataLoaderInterface
{
    /**
     * @param string $file
     * 
     * @param string $sheet
     * 
     * @return array
     */
    public function getRows(string $file, string $sheet): array;
    
}