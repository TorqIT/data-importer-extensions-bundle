<?php

namespace TorqIT\DataImporterExtensionsBundle\DataSource\Interpreter;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[Autoconfigure(calls: [['setLogger', ['@logger']]])]
#[AutoconfigureTag('monolog.logger', ['channel' => 'DATA-IMPORTER'])]
#[AutoconfigureTag('pimcore.datahub.data_importer.interpreter', ['type' => 'bulkCsv'])]
class BulkCsvFileInterpreter extends AbstractBulkCsvFileInterpreter {}
