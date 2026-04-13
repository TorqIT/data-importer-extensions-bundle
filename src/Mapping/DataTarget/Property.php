<?php



namespace TorqIT\DataImporterExtensionsBundle\Mapping\DataTarget;

use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Data\QuantityValue;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Bundle\DataImporterBundle\Mapping\DataTarget\DataTargetInterface;

class Property implements DataTargetInterface
{
    /**
     * @var string
     */
    protected $propertyName;

    /**
     * @param array $settings
     *
     * @throws InvalidConfigurationException
     */
    public function setSettings(array $settings): void
    {
        if (empty($settings['propertyName'])) {
            throw new InvalidConfigurationException('Empty property name.');
        }

        $this->propertyName = $settings['propertyName'];
    }

    /**
     * @param ElementInterface $element
     * @param mixed $data
     *
     * @return void
     *
     * @throws InvalidConfigurationException
     */
    public function assignData(ElementInterface $element, $data): void
    {
        $element->setProperty($this->propertyName, "text", $data);
    }

}
