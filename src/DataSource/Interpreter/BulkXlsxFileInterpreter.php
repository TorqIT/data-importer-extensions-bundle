<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace TorqIT\DataImporterExtensionsBundle\DataSource\Interpreter;

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Carbon\Carbon;
use Pimcore\Bundle\DataImporterBundle\Processing\ImportProcessingService;
use Pimcore\Db;

class BulkXlsxFileInterpreter extends \Pimcore\Bundle\DataImporterBundle\DataSource\Interpreter\XlsxFileInterpreter
{

    /**
     * @var array
     */
    protected $uniqueColumns;

    /**
     * @var array
     */
    protected $uniqueHashes;


    /**
     * @var string
     */
    protected $rowFilter;

    /**
     * @var int
     */
    private $batchCount = 0;

    /**
     * @var int
     */
    private $batchSize = 500;

    protected function doInterpretFileAndCallProcessRow(string $path): void
    {
        $this->uniqueHashes = array();

        $data=array();
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open($path);

        foreach($reader->getSheetIterator() as $sheet){
            if($sheet->getName() != $this->sheetName){
                continue;
            }

            foreach($sheet->getRowIterator() as $row){
                $cells = $row->getCells();
                $dataRow = [];
                foreach ($cells as $cell) {
                    $dataRow[] = $cell->getValue();
                }
                $data[]=$dataRow;
            }
        }

        $reader->close();

        if ($this->skipFirstRow) {
            array_shift($data);
        }

        $writer = WriterEntityFactory::createCSVWriter();
        $writer->setFieldEnclosure("'");
        $writer->openToFile('/var/www/html/test.csv');
        /** @var Carbon $carbonNow */
        $carbonNow = Carbon::now();
        $db = Db::get();

        foreach ($data as $rowData) {
            
            $hashKey = '';

            foreach($this->uniqueColumns as $index){
                $hashKey .= $rowData[$index];
            }

            if($hashKey !== '' && array_key_exists($hashKey, $this->uniqueHashes)){
                continue;
            }

            if(strlen($this->rowFilter) > 0){
                $expressionLanguage = new ExpressionLanguage();

                $filterResult = $expressionLanguage->evaluate($this->rowFilter, ['row' => $rowData]);

                if(!$filterResult){

                    continue;
                }
            }

            $json =  json_encode($rowData);

            $c = WriterEntityFactory::createCell($json);
            $c->setValue($c->getValue());
            $cells = [
                WriterEntityFactory::createCell((int)($carbonNow->getTimestamp() . str_pad((string)$carbonNow->milli, 3, '0'))),
                WriterEntityFactory::createCell($this->configName),
                $c,
                WriterEntityFactory::createCell($this->executionType),
                WriterEntityFactory::createCell(ImportProcessingService::JOB_TYPE_PROCESS)
            ];

           // $this->processImportRow($rowData);

            $singleRow = WriterEntityFactory::createRow($cells);
            $writer->addRow($singleRow);

            $this->uniqueHashes[$hashKey]=true;
        }

        $writer->close();

        $sql = <<<SQL
        LOAD DATA LOCAL INFILE '/var/www/html/test.csv' INTO TABLE bundle_data_hub_data_importer_queue
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
        parent::setSettings($settings);
 
        $this->rowFilter = $settings['rowFilter'] ?? '';

        if($settings['uniqueColumns'] && strlen($settings['uniqueColumns'] ) > 0){
            $this->uniqueColumns = explode(",", $settings["uniqueColumns"]);
        }
        else{
            $this->uniqueColumns = array();
        }        
    }
}
