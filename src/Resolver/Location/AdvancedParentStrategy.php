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

namespace TorqIT\DataImporterExtensionsBundle\Resolver\Location;

use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Bundle\DataImporterBundle\Tool\DataObjectLoader;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Service;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Bundle\DataImporterBundle\Resolver\Location\LocationStrategyInterface;
use Pimcore\Model\Element\Service as ElementService;
use TorqIT\DataImporterExtensionsBundle\Helper\AdvancedPathBuilder;

class AdvancedParentStrategy implements LocationStrategyInterface
{
    /**
     * @var string
     */
    protected $advancedParent;

    /**
     * @var string
     */
    protected $fallbackPath;


    /**
     * @param DataObjectLoader $dataObjectLoader
     */
    public function __construct(protected DataObjectLoader $dataObjectLoader)
    {
    }

    public function setSettings(array $settings): void
    {
        if (empty($settings['advancedParent'])) {
            throw new InvalidConfigurationException('No advanced parent');
        }

        $this->advancedParent = $settings['advancedParent'];

        $this->fallbackPath = $settings['fallbackPath'] ?? null;
    }

    public function updateParent(ElementInterface $element, array $inputData): ElementInterface
    {
        $newParent = null;

        $path = AdvancedPathBuilder::buildPath($inputData, $this->advancedParent);

        $newParent = $this->dataObjectLoader->loadByPath($path);

        if (!($newParent instanceof DataObject) && $path) {
            $newParent = Service::createFolderByPath($path);
        }

        if (!($newParent instanceof DataObject) && $this->fallbackPath) {
            $newParent = DataObject::getByPath($this->fallbackPath);
        }

        if ($newParent) {
            return $element->setParent($newParent);
        }

        return $element;
    }

    protected function loadById()
    {
        
    }
}
