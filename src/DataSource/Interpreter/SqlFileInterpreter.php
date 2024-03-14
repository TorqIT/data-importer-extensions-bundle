<?php

namespace TorqIT\DataImporterExtensionsBundle\DataSource\Interpreter;

use TorqIT\DataImporterExtensionsBundle\DataSource\Interpreter\BulkCsvFileInterpreter;

class SqlFileInterpreter extends BulkCsvFileInterpreter
{
    public function setSettings(array $settings): void
    {
        $this->skipFirstRow = true;
        $this->delimiter = ',';
        $this->enclosure = '"';
        $this->escape = '\\';
    }
}
