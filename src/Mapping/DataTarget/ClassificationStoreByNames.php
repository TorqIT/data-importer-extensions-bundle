<?php

namespace TorqIT\DataImporterExtensionsBundle\Mapping\DataTarget;

use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Bundle\DataImporterBundle\Mapping\DataTarget\DataTargetInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Classificationstore\GroupConfig;
use Pimcore\Model\DataObject\Classificationstore\KeyConfig;
use Pimcore\Model\Element\ElementInterface;

class ClassificationStoreByNames implements DataTargetInterface
{
    /**
     * @var string
     */
    protected $fieldName;

    /**
     * @var string
     */
    protected $language;

    /**
     * @var string
     */
    protected $groupName;

    /**
     * @var string
     */
    protected $keyName;

    public function setSettings(array $settings): void
    {
        if (empty($settings['fieldName'])) {
            throw new InvalidConfigurationException('Empty field name.');
        }

        if (empty($settings['groupName']) || empty($settings['keyName'])) {
            throw new InvalidConfigurationException('Empty group or key name.');
        }

        $this->fieldName = $settings['fieldName'];
        $this->groupName = $settings['groupName'];
        $this->keyName = $settings['keyName'];
        $this->language = $settings['language'] ?? null;
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

        if (!$element instanceof DataObject) {
            throw new InvalidConfigurationException('Element is not a data object.');
        }

        $storeId = $element->getClass()->getFieldDefinition($this->fieldName)->storeId;
        $groupId = GroupConfig::getByName($this->groupName, $storeId)?->getId();
        $keyId = KeyConfig::getByName($this->keyName, $storeId)?->getId();
        if (!$groupId || !$keyId) {
            throw new InvalidConfigurationException('Group or key not found.');
        }

        if ($classificationStore instanceof \Pimcore\Model\DataObject\Classificationstore) {
            $classificationStore->setLocalizedKeyValue($groupId, $keyId, $data, $this->language);
            $classificationStore->setActiveGroups($classificationStore->getActiveGroups() + [$groupId => true]);
        } else {
            throw new InvalidConfigurationException('Field ' . $this->fieldName . ' is not a classification store.');
        }
    }
}