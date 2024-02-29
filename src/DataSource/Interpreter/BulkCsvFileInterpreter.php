<?php

namespace TorqIT\DataImporterExtensionsBundle\DataSource\Interpreter;

use Pimcore\Bundle\DataImporterBundle\DataSource\Interpreter\CsvFileInterpreter;
use Pimcore\Db;

class BulkCsvFileInterpreter extends CsvFileInterpreter
{
    protected function doInterpretFileAndCallProcessRow(string $path): void
    {
        $db = Db::get();

        $quote = "'";
        $sql = <<<SQL
        LOAD DATA LOCAL INFILE $quote$path$quote INTO TABLE bundle_data_hub_data_importer_queue
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