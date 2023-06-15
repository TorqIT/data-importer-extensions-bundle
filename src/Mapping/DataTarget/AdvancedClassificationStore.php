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

namespace TorqIT\DataImporterExtensionsBundle\Mapping\DataTarget;

use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Bundle\DataImporterBundle\Mapping\DataTarget\Classificationstore as ClassificationStoreDataTarget;
use Pimcore\Model\DataObject\Data\QuantityValue;


class AdvancedClassificationStore extends ClassificationStoreDataTarget
{
    
    /**
     * @var bool
     */
    protected $writeIfSourceIsEmpty;

    /**
     * @var bool
     */
    protected $writeIfTargetIsNotEmpty;

    /**
     * @param array $settings
     *
     * @throws InvalidConfigurationException
     */
    public function setSettings(array $settings): void
    {
        parent::setSettings($settings);

        //note - cannot be replaced with ?? as $settings['writeIfSourceIsEmpty'] can be false on purpose
        $this->writeIfSourceIsEmpty = isset($settings['writeIfSourceIsEmpty']) ? $settings['writeIfSourceIsEmpty'] : true;
        $this->writeIfTargetIsNotEmpty = isset($settings['writeIfTargetIsNotEmpty']) ? $settings['writeIfTargetIsNotEmpty'] : true;
    }

    /**
     * @param ElementInterface $element
     * @param mixed $data
     *
     * @return void
     *
     * @throws InvalidConfigurationException
     */
    public function assignData(ElementInterface $element, $data)
    {
        $getter = 'get' . ucfirst($this->fieldName);
        $classificationStore = $element->$getter();

        if (!($classificationStore instanceof \Pimcore\Model\DataObject\Classificationstore)) {
            throw new InvalidConfigurationException('Field ' . $this->fieldName . ' is not a classification store.');
        }

        $currentValue = $classificationStore->getLocalizedKeyValue($this->groupId, $this->keyId);
        
        if(!$this->shouldAssignData($data, $currentValue)) {
            return;
        }

        parent::assignData($element, $data);
    }

    protected function shouldAssignData($newValue, $currentValue)
    {
        if ($this->writeIfTargetIsNotEmpty === true && $this->writeIfSourceIsEmpty === true) {
            return true;
        }

        if (!empty($currentValue) && $this->writeIfTargetIsNotEmpty === false) {
            return false;
        }
        if ($this->writeIfSourceIsEmpty === false && (empty($newValue) || ($newValue instanceof QuantityValue && empty($newValue->getValue())))) {
            return false;
        }

        return true;
    }
}
