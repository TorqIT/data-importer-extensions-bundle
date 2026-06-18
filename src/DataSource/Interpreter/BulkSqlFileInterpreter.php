<?php

namespace TorqIT\DataImporterExtensionsBundle\DataSource\Interpreter;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(tags: [
    ['name' => 'monolog.logger', 'attributes' => ['channel' => 'DATA-IMPORTER']],
    ['name' => 'pimcore.datahub.data_importer.interpreter', 'attributes' => ['type' => 'bulkCsv']],
])]
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
