<?php

namespace TorqIT\DataImporterExtensionsBundle\DataSource\DataLoader\Xlsx;


interface XlsxDataLoaderInterface
{
    /**
     * Returns the rows of the given sheet one by one. Implementations should stream
     * (yield) rows instead of building the full array in memory, so large files do
     * not exhaust the memory limit.
     *
     * @param string $file
     *
     * @param string $sheet
     *
     * @return iterable<array>
     */
    public function getRows(string $file, string $sheet): iterable;

}
