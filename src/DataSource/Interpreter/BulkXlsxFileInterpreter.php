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

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Carbon\Carbon;
use Pimcore\Bundle\DataImporterBundle\Processing\ImportProcessingService;
use Pimcore\Db;
use TorqIT\DataImporterExtensionsBundle\DataSource\DataLoader\Xlsx\XlsxDataLoaderFactory;

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
     * @var boolean
     */
    protected $lowMemoryReader = false;

    protected function doInterpretFileAndCallProcessRow(string $path): void
    {
        $this->uniqueHashes = array();
        
        $excelLoader = XlsxDataLoaderFactory::getExcelDataLoader($this->lowMemoryReader);
        $data = $excelLoader->getRows($path, $this->sheetName);

        if ($this->skipFirstRow) {
            array_shift($data);
        }

        $tmpCsv = tempnam(sys_get_temp_dir(), 'pimcore_bulk_load');
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->setFieldEnclosure("'");
        $writer->openToFile($tmpCsv);
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

            $singleRow = WriterEntityFactory::createRow($cells);
            $writer->addRow($singleRow);

            $this->uniqueHashes[$hashKey]=true;
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

        $db->executeQuery($sql);

        unlink($tmpCsv);
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
        
        $this->lowMemoryReader = isset($settings['lowMemoryReader']) ? $settings['lowMemoryReader'] : false;
        
    }
}