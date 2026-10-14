<?php



namespace TorqIT\DataImporterExtensionsBundle\DataSource\Interpreter;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use TorqIT\DataImporterExtensionsBundle\DataSource\DataLoader\Xlsx\XlsxDataLoaderFactory;

#[Autoconfigure(calls: [['setLogger', ['@logger']]])]
#[AutoconfigureTag('monolog.logger', ['channel' => 'DATA-IMPORTER'])]
#[AutoconfigureTag('pimcore.datahub.data_importer.interpreter', ['type' => 'advancedXlsx'])]
class AdvancedXlsxFileInterpreter extends XlsxFileInterpreterWithColumnNames
{
    protected array $uniqueColumns;
    protected array $uniqueHashes;
    protected string $rowFilter;

    protected function doInterpretFileAndCallProcessRow(string $path): void
    {
        $this->uniqueHashes = array();

        $excelLoader = XlsxDataLoaderFactory::getExcelDataLoader();

        $expressionLanguage = strlen($this->rowFilter) > 0 ? new ExpressionLanguage() : null;
        $headerRow = null;
        $rowNumber = 0;

        // Rows are streamed one by one so large files never have to be loaded into memory at once.
        foreach ($excelLoader->getRows($path, $this->sheetName) as $rowData) {
            $rowNumber++;

            // Rows up to and including the header row (1-indexed) are skipped
            if ($rowNumber <= $this->headerRow) {
                if ($rowNumber === $this->headerRow && $this->saveHeaderName) {
                    $headerRow = $rowData;
                }
                continue;
            }

            $hashKey = '';

            foreach($this->uniqueColumns as $index){
                $hashKey .= $rowData[$index];
            }

            if($hashKey !== '' && array_key_exists($hashKey, $this->uniqueHashes)){
                continue;
            }

            if($expressionLanguage !== null){
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
