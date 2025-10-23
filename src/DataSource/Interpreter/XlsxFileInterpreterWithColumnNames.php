<?php


/*
 * This class allows one definition of previewData() to be shared by all extending XLS Interpreters.
 */

 namespace TorqIT\DataImporterExtensionsBundle\DataSource\Interpreter;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Pimcore\Bundle\DataImporterBundle\Preview\Model\PreviewData;
use Pimcore\Bundle\DataImporterBundle\DataSource\Interpreter\XlsxFileInterpreter;

abstract class XlsxFileInterpreterWithColumnNames extends XlsxFileInterpreter
{
    /**
     *
     * @var bool
     *
     * If true, index columns by the header name in the first row, instead of using numbered index.
     */
    protected bool $saveHeaderName;

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
            $reader->setLoadSheetsOnly($this->sheetName);
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

    public function setSettings(array $settings) : void
    {
        parent::setSettings($settings);

        $this->saveHeaderName = $settings['saveHeaderName'] ?? false;
    }
}
