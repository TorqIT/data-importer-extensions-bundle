<?php

namespace TorqIT\DataImporterExtensionsBundle\Mapping\DataTarget;

use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Bundle\DataImporterBundle\Mapping\DataTarget\DataTargetInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Tool;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'pimcore.datahub.data_importer.data_target', attributes: ['type' => 'dynamicLocalizedField'])]
class DynamicLocalizedField implements DataTargetInterface
{
    protected string $fieldName;

    /**
     * @param array $settings
     * @throws InvalidConfigurationException
     */
    public function setSettings(array $settings): void
    {
        if (empty($settings['fieldName'])) {
            throw new InvalidConfigurationException('DynamicLocalizedField: Empty field name.');
        }

        $this->fieldName = $settings['fieldName'];
    }

    /**
     * Assign data to a localized field using a dynamically provided language.
     *
     * Expects $data to be an array:
     *   $data[0] = the value to set on the field
     *   $data[1] = the language/locale code (e.g. 'en', 'de')
     *
     * @param ElementInterface $element
     * @param mixed $data
     * @throws InvalidConfigurationException
     */
    public function assignData(ElementInterface $element, $data): void
    {
        if (!is_array($data) || count($data) < 2) {
            throw new InvalidConfigurationException(
                sprintf(
                    'DynamicLocalizedField "%s" expects two source attribute values: [0] = value, [1] = language code. Received: %s',
                    $this->fieldName,
                    json_encode($data)
                )
            );
        }

        $value = $data[0];
        $language = $data[1];

        if (empty($language)) {
            throw new InvalidConfigurationException(
                sprintf('DynamicLocalizedField "%s": language value is empty.', $this->fieldName)
            );
        }

        $validLanguages = Tool::getValidLanguages();

        if (!in_array($language, $validLanguages, true)) {
            throw new InvalidConfigurationException(
                sprintf(
                    'DynamicLocalizedField "%s": ERROR: language "%s" doesn\'t exist. Available languages: %s',
                    $this->fieldName,
                    $language,
                    implode(', ', $validLanguages)
                )
            );
        }

        if (!($element instanceof DataObject\Concrete)) {
            throw new InvalidConfigurationException(
                'DynamicLocalizedField only supports DataObject\\Concrete elements.'
            );
        }

        $setter = 'set' . ucfirst($this->fieldName);
        $element->$setter($value, $language);
    }
}
