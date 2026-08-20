<?php

declare(strict_types=1);

namespace TorqIT\DataImporterExtensionsBundle\Mapping\DataTarget;

use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Bundle\DataImporterBundle\Mapping\DataTarget\Direct;
use Pimcore\Model\DataObject\Objectbrick;
use Pimcore\Model\Element\ElementInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'pimcore.datahub.data_importer.data_target', attributes: ['type' => 'objectBrickField'])]
class ObjectBrickField extends Direct
{
    protected string $brickField;

    /** @throws InvalidConfigurationException */
    public function setSettings(array $settings): void
    {
        if (empty($settings['brickField'])) {
            throw new InvalidConfigurationException('Empty brick container field name.');
        }

        $this->brickField = $settings['brickField'];

        parent::setSettings($settings);
    }

    /**
     * Writes the value into the configured attribute of whatever brick type is
     * currently set in the brick container, without the mapping having to know
     * the brick type up front (e.g. a brick resolved by the objectBrick data
     * target earlier in the mapping).
     *
     * @throws InvalidConfigurationException
     */
    public function assignData(ElementInterface $element, $data): void
    {
        $containerGetter = 'get' . ucfirst($this->brickField);
        $brickContainer = $element->$containerGetter();

        if (!$brickContainer instanceof Objectbrick) {
            throw new InvalidConfigurationException(
                sprintf('Field "%s" is not an object brick container.', $this->brickField)
            );
        }

        $attributeName = $this->fieldName;

        try {
            foreach ($brickContainer->getItems() as $brick) {
                if ($brick->getDoDelete()) {
                    continue;
                }

                if ($brick->getDefinition()->getFieldDefinition($attributeName) === null) {
                    continue;
                }

                $this->fieldName = sprintf('%s.%s.%s', $this->brickField, $brick->getType(), $attributeName);
                parent::assignData($element, $data);
            }
        } finally {
            $this->fieldName = $attributeName;
        }
    }
}
