<?php

namespace TorqIT\DataImporterExtensionsBundle\DataSource\Interpreter;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('monolog.logger', ['channel' => 'DATA-IMPORTER'])]
#[AutoconfigureTag('pimcore.datahub.data_importer.interpreter', ['type' => 'bulkSql'])]
class BulkSqlFileInterpreter extends AbstractBulkCsvFileInterpreter
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
