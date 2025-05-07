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

use PhpOffice\PhpSpreadsheet\IOFactory;
use Pimcore\Bundle\DataImporterBundle\Preview\Model\PreviewData;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use TorqIT\DataImporterExtensionsBundle\DataSource\DataLoader\Xlsx\XlsxDataLoaderFactory;

class AdvancedXlsxFileInterpreter extends \Pimcore\Bundle\DataImporterBundle\DataSource\Interpreter\XlsxFileInterpreter
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

            if( !is_null($headerRow) ) {
                $rowData = array_combine($headerRow, $rowData);
            }

            $this->processImportRow($rowData);

            $this->uniqueHashes[$hashKey]=true;
        }
    }

    /* Overriding parent method to allow for using column header names as keys */
    public function previewData(string $path, int $recordNumber = 0, array $mappedColumns = []): PreviewData
    {
        $previewData = [];
        $columns = [];
        $readRecordNumber = 0;
        $headerRow = null;

        if ($this->fileValid($path)) {
            $reader = IOFactory::createReaderForFile($path);
            $reader->setReadDataOnly(true);
            $spreadSheet = $reader->load($path);

            $spreadSheet->setActiveSheetIndexByName($this->sheetName);

            $data = $spreadSheet->getActiveSheet()->toArray();

            if ($this->skipFirstRow) {
                $firstRow = array_shift($data);

                // if set to do so, use the first row as header names
                if ($this->saveHeaderName) {
                    $headerRow = $firstRow;
                    foreach ($firstRow as $index => $columnHeader) {
                        $columns[$columnHeader] = trim($columnHeader);
                    }
                } else {
                    foreach ($firstRow as $index => $columnHeader) {
                        $columns[$index] = trim($columnHeader) . " [$index]";
                    }
                }


            }

            $previewDataRow = $data[$recordNumber] ?? null;

            if (empty($previewDataRow)) {
                $previewDataRow = end($data);
                $readRecordNumber = count($data) - 1;
            } else {
                $readRecordNumber = $recordNumber;
            }

            // if we have column names, use them as the keys for the data array
            if( $headerRow !== null){
                $previewDataRow = array_combine($headerRow, $previewDataRow);
            }

            foreach ($previewDataRow as $index => $columnData) {
                $previewData[$index] = $columnData;
            }

            if (empty($columns)) {
                $columns = array_keys($previewData);
            }
        }

        return new PreviewData($columns, $previewData, $readRecordNumber, $mappedColumns);
    }

    public function setSettings(array $settings): void
    {
        parent::setSettings($settings);

        $this->saveHeaderName = $settings['saveHeaderName'] ?? false;

        $this->rowFilter = $settings['rowFilter'] ?? '';

        if($settings['uniqueColumns'] && strlen($settings['uniqueColumns'] ) > 0){
            $this->uniqueColumns = explode(",", $settings["uniqueColumns"]);
        }
        else{
            $this->uniqueColumns = array();
        }
    }
}
