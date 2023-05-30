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
use PhpOffice\PhpSpreadsheet\IOFactory;
use Pimcore\Bundle\DataImporterBundle\Exception\InvalidInputException;
use Symfony\Component\Config\Util\XmlUtils;

class PiesFileInterpreter extends \Pimcore\Bundle\DataImporterBundle\DataSource\Interpreter\XmlFileInterpreter
{

    /**
     * @param string $path
     *
     * @return \DOMNodeList
     *
     * @throws InvalidInputException
     */
    protected function loadData(string $path)
    {
        if ($this->cachedFilePath === $path && !empty($this->cachedContent)) {
            $schema = $this->schema;
            $dom = XmlUtils::loadFile($path, function ($dom) use ($schema) {
                if (!empty($schema)) {
                    return @$dom->schemaValidateSource($schema);
                }

                return true;
            });
        } else {
            $dom = $this->cachedContent;
        }

        $xpath = new \DOMXpath($dom);
        $xpath->registerNamespace('p', 'http://www.autocare.org');

        $result = $xpath->evaluate($this->xpath);

        if ($result instanceof \DOMNodeList) {
            return $result;
        } else {
            throw new InvalidInputException(sprintf('Item path `%s` not found.', $this->xpath));
        }
    }

    
}
