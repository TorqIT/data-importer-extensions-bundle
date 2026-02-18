<?php



namespace TorqIT\DataImporterExtensionsBundle\DataSource\Interpreter;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Pimcore\Bundle\DataImporterBundle\Preview\Model\PreviewData;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use TorqIT\DataImporterExtensionsBundle\DataSource\DataLoader\Xlsx\XlsxDataLoaderFactory;

class AdvancedXlsxFileInterpreter extends XlsxFileInterpreterWithColumnNames
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

    protected function doInterpretFileAndCallProcessRow(string $path): void
    {
        $this->uniqueHashes = array();

        $excelLoader = XlsxDataLoaderFactory::getExcelDataLoader();
        $data = $excelLoader->getRows($path, $this->sheetName);

        // Header row is 1-indexed, array is 0-indexed
        $headerRowIndex = $this->headerRow - 1;

        // Get header row for column names
        $headerRow = null;
        if ($this->saveHeaderName && isset($data[$headerRowIndex])) {
            $headerRow = $data[$headerRowIndex];
        }

        // Skip rows up to and including the header row
        $data = array_slice($data, $this->headerRow);

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

            if (!is_null($headerRow)) {
                if (count($headerRow) > count($rowData)) {
                    $rowData = array_pad($rowData, count($headerRow), null);
                } elseif (count($headerRow) < count($rowData)) {
                    $rowData = array_slice($rowData, 0, count($headerRow));
                }
                $rowData = array_combine($headerRow, $rowData);
            }

            $this->processImportRow($rowData);

            $this->uniqueHashes[$hashKey]=true;
        }
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
