<?php

namespace TorqIT\DataImporterExtensionsBundle\Mapping\DataTarget;

use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Bundle\DataImporterBundle\Mapping\DataTarget\Direct;
use Pimcore\Model\Element\ElementInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'pimcore.datahub.data_importer.data_target', attributes: ['type' => 'fieldCollection'])]
class FieldCollection extends Direct
{
    /** @throws InvalidConfigurationException */
    public function assignData(ElementInterface $element, $data): void
    {
        parent::assignData($element, $data);
    }

    /** @throws InvalidConfigurationException */
    public function setSettings(array $settings): void
    {
        parent::setSettings($settings);
    }
}
