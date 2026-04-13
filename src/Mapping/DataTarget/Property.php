<?php



namespace TorqIT\DataImporterExtensionsBundle\Mapping\DataTarget;

use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Bundle\DataImporterBundle\Mapping\DataTarget\DataTargetInterface;
use Pimcore\Model\Element\ElementInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'pimcore.datahub.data_importer.data_target', attributes: ['type' => 'property'])]
class Property implements DataTargetInterface
{
    protected string $propertyName;

    /** @throws InvalidConfigurationException */
    public function setSettings(array $settings): void
    {
        if (empty($settings['propertyName'])) {
            throw new InvalidConfigurationException('Empty property name.');
        }

        $this->propertyName = $settings['propertyName'];
    }

    public function assignData(ElementInterface $element, $data): void
    {
        $element->setProperty($this->propertyName, "text", $data);
    }

}
