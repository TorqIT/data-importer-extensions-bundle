<?php

namespace TorqIT\DataImporterExtensionsBundle\DataSource\Interpreter;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[Autoconfigure(calls: [['setLogger', ['@logger']]])]
#[AutoconfigureTag(name: 'monolog.logger', attributes: ['channel' => 'DATA-IMPORTER'])]
#[AutoconfigureTag(name: 'pimcore.datahub.data_importer.operator', attributes: ['type' => 'safeKey'])]
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
