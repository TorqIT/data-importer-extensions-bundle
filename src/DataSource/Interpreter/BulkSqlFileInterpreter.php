<?php

namespace TorqIT\DataImporterExtensionsBundle\DataSource\Interpreter;

// Tagging happens in Resources/config/services.yml with autoconfigure: false so this
// class does not inherit the parent's `bulkCsv` interpreter tag.
class BulkSqlFileInterpreter extends BulkCsvFileInterpreter
{
    use BulkCsvLoadingTrait;

    public function setSettings(array $settings): void
    {
        $this->skipFirstRow = true;
        $this->saveHeaderName = false;
        $this->delimiter = ',';
        $this->enclosure = '"';
        $this->escape = '\\';
    }
}
