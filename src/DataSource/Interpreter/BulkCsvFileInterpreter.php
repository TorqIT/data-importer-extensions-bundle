<?php

namespace TorqIT\DataImporterExtensionsBundle\DataSource\Interpreter;

use Pimcore\Bundle\DataImporterBundle\DataSource\Interpreter\CsvFileInterpreter;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[Autoconfigure(calls: [['setLogger', ['@logger']]])]
#[AutoconfigureTag(name: 'monolog.logger', attributes: ['channel' => 'DATA-IMPORTER'])]
#[AutoconfigureTag(name: 'pimcore.datahub.data_importer.interpreter', attributes: ['type' => 'bulkCsv'])]
// FIXME: CsvFileInterpreter is now final, cannot extend
class BulkCsvFileInterpreter
{
    use BulkCsvLoadingTrait;
}
