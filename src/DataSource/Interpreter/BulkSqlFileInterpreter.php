<?php

namespace TorqIT\DataImporterExtensionsBundle\DataSource\Interpreter;

use TorqIT\DataImporterExtensionsBundle\DataSource\Interpreter\BulkCsvFileInterpreter;

class BulkSqlFileInterpreter extends BulkCsvFileInterpreter
{
    public function setSettings(array $settings): void
    {
        $this->skipFirstRow = true;
        $this->saveHeaderName = false;
        $this->delimiter = ',';
        $this->enclosure = '"';
        $this->escape = '\\';
    }
}
