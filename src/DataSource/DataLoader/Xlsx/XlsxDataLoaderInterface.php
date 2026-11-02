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

    /**
     * Yields the rows one at a time, keyed from 0, so callers can stop early without loading the whole sheet.
     *
     * @return iterable<int, array>
     */
    public function iterateRows(string $file, string $sheet): iterable;

}
