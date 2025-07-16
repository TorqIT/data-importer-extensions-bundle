<?php

namespace TorqIT\DataImporterExtensionsBundle\Mapping\DataTarget;

use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Bundle\DataImporterBundle\Mapping\DataTarget\DataTargetInterface;
use Pimcore\Model\DataObject;

class FieldCollection implements DataTargetInterface
{
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
        $this->setFieldCollection($element, $data);
    }

    private function setFieldCollection(DataObject\Product $product, $data)
    {
        $fieldCollection = new DataObject\Fieldcollection();

        $dataDecoded = json_decode($data, true);
    }

    /**
     * @param array $settings
     *
     * @throws InvalidConfigurationException
     */
    public function setSettings(array $settings): void {}
}
