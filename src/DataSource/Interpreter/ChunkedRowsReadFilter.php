<?php

namespace TorqIT\DataImporterExtensionsBundle\DataSource\Interpreter;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

/**
 * Restricts spreadsheet reading to a contiguous row range so imports can be
 * processed chunk by chunk instead of loading the whole workbook into memory.
 */
class ChunkedRowsReadFilter implements IReadFilter
{
    public function __construct(
        private readonly int $startRow,
        private readonly int $endRow,
    ) {
    }

    public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool
    {
        return $row >= $this->startRow && $row <= $this->endRow;
    }
}
