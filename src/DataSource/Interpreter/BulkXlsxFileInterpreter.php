<?php declare(strict_types=1);

namespace TorqIT\DataImporterExtensionsBundle\DataSource\Interpreter;

use Carbon\Carbon;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\CSV\Options;
use OpenSpout\Writer\CSV\Writer;
use Pimcore\Bundle\DataImporterBundle\Processing\ImportProcessingService;
use Pimcore\Db;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[Autoconfigure(calls: [['setLogger', ['@logger']]])]
#[AutoconfigureTag('monolog.logger', ['channel' => 'DATA-IMPORTER'])]
#[AutoconfigureTag('pimcore.datahub.data_importer.interpreter', ['type' => 'bulkXlsx'])]
class BulkXlsxFileInterpreter extends StreamingXlsxFileInterpreter
{
    protected function doInterpretFileAndCallProcessRow(string $path): void
    {
        $tmpCsv = tempnam(sys_get_temp_dir(), 'pimcore_bulk_load');
        $options = new Options();
        $options->FIELD_ENCLOSURE = "'";
        $writer = new Writer($options);
        $writer->openToFile($tmpCsv);
        $carbonNow = Carbon::now();
        $timestamp = (int)($carbonNow->getTimestamp() . str_pad((string)$carbonNow->milli, 3, '0'));

        foreach ($this->streamImportRows($path) as $rowData) {
            $writer->addRow(new Row([
                Cell::fromValue($timestamp),
                Cell::fromValue($this->configName),
                Cell::fromValue(json_encode($rowData)),
                Cell::fromValue($this->executionType),
                Cell::fromValue(ImportProcessingService::JOB_TYPE_PROCESS),
            ]));
        }

        $writer->close();

        $quote = "'";
        $sql = <<<SQL
        LOAD DATA LOCAL INFILE $quote$tmpCsv$quote INTO TABLE bundle_data_hub_data_importer_queue
            FIELDS
                TERMINATED BY ','
                ENCLOSED BY "'"
                ESCAPED BY ''
            LINES TERMINATED BY '\n'

            (timestamp, configName, data, executionType, jobType)
        SQL;

        Db::get()->executeQuery($sql);

        unlink($tmpCsv);
    }
}
