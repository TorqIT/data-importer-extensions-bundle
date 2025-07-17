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

use Carbon\Carbon;
use OpenSpout\Writer\CSV\Options;
use OpenSpout\Writer\CSV\Writer;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Pimcore\Bundle\DataImporterBundle\Processing\ImportProcessingService;
use Pimcore\Db;
use TorqIT\DataImporterExtensionsBundle\DataSource\DataLoader\Xlsx\XlsxDataLoaderFactory;
use Pimcore\Bundle\DataImporterBundle\Preview\Model\PreviewData;

class BulkXlsxFileInterpreter extends XlsxFileInterpreterWithColumnNames
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
     *
     * @var bool
     *
     * If true, index columns by the header name in the first row, instead of using numbered index.
     */
    protected bool $saveHeaderName;


    protected function doInterpretFileAndCallProcessRow(string $path): void
    {
        $this->uniqueHashes = array();

        $excelLoader = XlsxDataLoaderFactory::getExcelDataLoader();
        $data = $excelLoader->getRows($path, $this->sheetName);
        $headerRow = null;

        if ($this->skipFirstRow) {
            $firstRow = array_shift($data);

            if ($this->saveHeaderName) {
                $headerRow = $firstRow;
            }
        }

        $tmpCsv = tempnam(sys_get_temp_dir(), 'pimcore_bulk_load');
        $options = new Options();
        $options->FIELD_ENCLOSURE = "'";
        $writer = new Writer($options);
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

            // TO use header names as keys, we set the keys for rowData:
            if (!is_null($headerRow)) {
                if (count($headerRow) > count($rowData)) {
                    $rowData = array_pad($rowData, count($headerRow), null);
                } elseif (count($headerRow) < count($rowData)) {
                    $rowData = array_slice($rowData, 0, count($headerRow));
                }
                $rowData = array_combine($headerRow, $rowData);
            }

            $json =  json_encode($rowData);

            $c = Cell::fromValue($json);

            $cells = [
                Cell::fromValue((int)($carbonNow->getTimestamp() . str_pad((string)$carbonNow->milli, 3, '0'))),
                Cell::fromValue($this->configName),
                $c,
                Cell::fromValue($this->executionType),
                Cell::fromValue(ImportProcessingService::JOB_TYPE_PROCESS)
            ];

            $singleRow = new Row($cells);
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

    }
}
