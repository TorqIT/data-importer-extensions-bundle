<?php

namespace TorqIT\DataImporterExtensionsBundle\DataSource\Interpreter;

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Pimcore\Bundle\DataImporterBundle\Preview\Model\PreviewData;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use TorqIT\DataImporterExtensionsBundle\DataSource\DataLoader\Xlsx\XlsxDataLoaderFactory;
use DOMXPath;
use DOMDocument;
use DOMNodeList;
use Symfony\Component\Config\Util\XmlUtils;
use Pimcore\Bundle\DataImporterBundle\PimcoreDataImporterBundle;
use Symfony\Component\Config\Util\Exception\XmlParsingException;
use Pimcore\Bundle\DataImporterBundle\Exception\InvalidInputException;
use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Symfony\Component\Validator\Constraints\Length;

class XMLSchemaBasedPreview extends \Pimcore\Bundle\DataImporterBundle\DataSource\Interpreter\XmlFileInterpreter
{

    public function previewData(string $path, int $recordNumber = 0, array $mappedColumns = []): PreviewData
    {
        $previewData = [];
        $columns = [];
        $readRecordNumber = 0;

        if ($this->fileValid($path)) {
            $records = $this->loadData($path);
            $previewDataItem = $records->item($recordNumber);

            if (empty($previewDataItem)) {
                $readRecordNumber = $records->count() - 1;
                $previewDataItem = $records->item($readRecordNumber);
            } else {
                $readRecordNumber = $recordNumber;
            }

            if (!empty($previewDataItem) && $previewDataItem instanceof \DOMElement) {
                $previewData = XmlUtils::convertDomElementToArray($previewDataItem);

                $keys = array_keys($previewData);
                $columns = array_combine($keys, $keys);
            }

            //merge in fields that arent on the current object
            if ($this->schema) {
                $doc = new DOMDocument();
                $doc->loadXML(mb_convert_encoding($this->schema, 'utf-8', mb_detect_encoding($this->schema)));
                $xpath = new DOMXPath($doc);
                $xpath->registerNamespace('xs', 'http://www.w3.org/2001/XMLSchema');
                //find desired nesting depth
                $domElements = $xpath->evaluate("/xs:schema/xs:element");

                $xPathParts = explode("/", $this->xpath);
                array_shift($xPathParts);
                $desiredElement = $domElements;
                foreach ($xPathParts as $xpathPart) {
                    $desiredElement = $this->getChildNode($desiredElement, $xpathPart);
                    $desiredElement = $xpath->evaluate("xs:complexType/xs:sequence/xs:element", $desiredElement);
                }
                if ($desiredElement) {
                    foreach ($desiredElement as $element) {
                        $columns[$element->getAttribute("name")] = $element->getAttribute("name");
                    }
                }
            }
        }

        return new PreviewData($columns, $previewData, $readRecordNumber, $mappedColumns);
    }

    private function getChildNode(DOMNodeList $domElements, string $childName)
    {
        foreach ($domElements as $domElement) {
            if ($domElement->getAttribute("name") == $childName) {
                return $domElement;
            }
        }
    }
}
