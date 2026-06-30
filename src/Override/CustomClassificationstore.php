<?php

namespace TorqIT\DataImporterExtensionsBundle\Override;

use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Bundle\DataImporterBundle\Mapping\DataTarget\DataTargetInterface;
use Pimcore\Model\Element\ElementInterface;

// Copy of \Pimcore\Bundle\DataImporterBundle\Mapping\DataTarget\Classificationstore
class CustomClassificationstore implements DataTargetInterface
{
    protected string $fieldName;
    protected string $language;
    protected int $keyId;
    protected int $groupId;

    public function setSettings(array $settings): void
    {
        if (empty($settings['fieldName'])) {
            throw new InvalidConfigurationException('Empty field name.');
        }

        $keyParts = explode('-', ($settings['keyId'] ?? []));
        if (empty($keyParts[0]) || empty($keyParts[1])) {
            throw new InvalidConfigurationException('Empty or invalid keyId.');
        }

        $this->fieldName = $settings['fieldName'];
        $this->groupId = (int)$keyParts[0];
        $this->keyId = (int)$keyParts[1];
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

        if ($classificationStore instanceof \Pimcore\Model\DataObject\Classificationstore) {
            $classificationStore->setLocalizedKeyValue($this->groupId, $this->keyId, $data, $this->language);
            $classificationStore->setActiveGroups($classificationStore->getActiveGroups() + [$this->groupId => true]);
        } else {
            throw new InvalidConfigurationException('Field ' . $this->fieldName . ' is not a classification store.');
        }
    }
}