<?php

namespace TorqIT\DataImporterExtensionsBundle\DataSource\Interpreter;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('monolog.logger', ['channel' => 'DATA-IMPORTER'])]
#[AutoconfigureTag('pimcore.datahub.data_importer.interpreter', ['type' => 'bulkCsv'])]
class BulkCsvFileInterpreter extends AbstractBulkCsvFileInterpreter {}
