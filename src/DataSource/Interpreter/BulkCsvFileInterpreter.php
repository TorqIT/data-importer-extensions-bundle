<?php

namespace TorqIT\DataImporterExtensionsBundle\DataSource\Interpreter;

use Carbon\Carbon;
use Pimcore\Bundle\DataImporterBundle\DataSource\Interpreter\CsvFileInterpreter;
use Pimcore\Bundle\DataImporterBundle\Processing\ImportProcessingService;
use Pimcore\Db;

class BulkCsvFileInterpreter extends CsvFileInterpreter
{
    protected function doInterpretFileAndCallProcessRow(string $path): void
    {
        $db = Db::get();
        $carbonNow = Carbon::now();
        $timestamp = (int)($carbonNow->getTimestamp() . str_pad((string)$carbonNow->milli, 3, '0'));
        $tmpCsv = tempnam(sys_get_temp_dir(), 'pimcore_bulk_load');

        if (($readHandle = fopen($path, 'r')) !== false && ($writeHandle = fopen($tmpCsv, 'w')) !== false) {
            while (($row = fgetcsv($readHandle)) !== false) {
                fputcsv($writeHandle, [
                    $timestamp,
                    $this->configName,
                    json_encode($row),
                    $this->executionType,
                    ImportProcessingService::JOB_TYPE_PROCESS
                ]);
            }
            fclose($writeHandle);
            fclose($readHandle);
        }

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

        $db->executeQuery($sql);
    }

    public function setSettings(array $settings): void
    {
        parent::setSettings([]);
    }
}