<?php

namespace TorqIT\DataImporterExtensionsBundle\DataSource\Interpreter;

use Carbon\Carbon;
use Pimcore\Bundle\DataImporterBundle\Processing\ImportProcessingService;
use Pimcore\Db;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TorqIT\DataImporterExtensionsBundle\Override\CustomCsvFileInterpreter;

#[Autoconfigure(tags: [
    ['name' => 'monolog.logger', 'attributes' => ['channel' => 'DATA-IMPORTER']],
    ['name' => 'pimcore.datahub.data_importer.interpreter', 'attributes' => ['type' => 'bulkSql']],
])]
class BulkCsvFileInterpreter extends CustomCsvFileInterpreter
{
    protected function doInterpretFileAndCallProcessRow(string $path): void
    {
        $db = Db::get();
        $carbonNow = Carbon::now();
        $timestamp = (int)($carbonNow->getTimestamp() . str_pad((string)$carbonNow->milli, 3, '0'));
        $tmpCsv = tempnam(sys_get_temp_dir(), 'pimcore_bulk_load');
        $writeDelimiter = $this->delimiter === ';' ? ',' : ';';
        $writeEnclosure = "'";
        $writeEscape = '';

        if (($readHandle = fopen($path, 'r')) !== false && ($writeHandle = fopen($tmpCsv, 'w')) !== false) {
            $header = null;
            $currentRow = 0;
            while (($row = fgetcsv(
                    $readHandle,
                    separator: $this->delimiter,
                    enclosure: $this->enclosure,
                    escape: $this->escape,
                )) !== false) {
                $currentRow++;
                if ($currentRow === 1 && $this->skipFirstRow) {
                    if ($this->saveHeaderName) {
                        $header = $row;
                    }
                    continue;
                }
                if ($header !== null) {
                    $row = array_combine($header, $row);
                }
                fputcsv(
                    $writeHandle,
                    [
                        $timestamp,
                        $this->configName,
                        json_encode($row),
                        $this->executionType,
                        ImportProcessingService::JOB_TYPE_PROCESS,
                    ],
                    separator: $writeDelimiter,
                    enclosure: $writeEnclosure,
                    escape: $writeEscape,
                );
            }
            fclose($writeHandle);
            fclose($readHandle);
        }

        $quote = "'";
        $sql = <<<SQL
            LOAD DATA LOCAL INFILE $quote$tmpCsv$quote INTO TABLE bundle_data_hub_data_importer_queue
                FIELDS 
                    TERMINATED BY '$writeDelimiter'
                    ENCLOSED BY "$writeEnclosure"
                    ESCAPED BY '$writeEscape'
                LINES TERMINATED BY '\n'
            
                (timestamp, configName, data, executionType, jobType)
            SQL;

        $db->executeQuery($sql);
        unlink($tmpCsv);
    }
}
