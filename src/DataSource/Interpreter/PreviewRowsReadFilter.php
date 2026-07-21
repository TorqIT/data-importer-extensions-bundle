<?php

namespace TorqIT\DataImporterExtensionsBundle\DataSource\Interpreter;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

/*
 * Restricts spreadsheet reading to the given row numbers so previews do not
 * need to load the whole workbook into memory.
 */
class PreviewRowsReadFilter implements IReadFilter
{
    /**
     * @var array<int, true>
     */
    private array $rows;

    /**
     * @param int[] $rows 1-based row numbers to read
     */
    public function __construct(array $rows)
    {
        $this->rows = array_fill_keys($rows, true);
    }

    public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool
    {
        return isset($this->rows[$row]);
    }
}
