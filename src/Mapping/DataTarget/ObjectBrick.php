<?php

declare(strict_types=1);

namespace TorqIT\DataImporterExtensionsBundle\Mapping\DataTarget;

use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Bundle\DataImporterBundle\Mapping\DataTarget\DataTargetInterface;
use Pimcore\Model\DataObject\Objectbrick as ObjectbrickContainer;
use Pimcore\Model\Element\ElementInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'pimcore.datahub.data_importer.data_target', attributes: ['type' => 'objectBrick'])]
class ObjectBrick implements DataTargetInterface
{
    protected string $fieldName;

    protected bool $removeOtherTypes;

    /** @throws InvalidConfigurationException */
    public function setSettings(array $settings): void
    {
        if (empty($settings['fieldName'])) {
            throw new InvalidConfigurationException('Empty object brick field name.');
        }

        $this->fieldName = $settings['fieldName'];
        $this->removeOtherTypes = (bool) ($settings['removeOtherTypes'] ?? false);
    }

    /**
     * Interprets the incoming value as an object brick type and ensures a brick of
     * that type exists in the configured brick container. Combine with a value
     * mapping operator (e.g. conditionalConversion) to resolve source values into
     * brick type names. Subsequent mapping rows can then write into the resolved
     * brick via the objectBrickField data target.
     *
     * @throws InvalidConfigurationException
     */
    public function assignData(ElementInterface $element, $data): void
    {
        if (is_array($data)) {
            $data = reset($data);
        }

        if (empty($data)) {
            return;
        }

        $brickType = ucfirst((string) $data);

        $containerGetter = 'get' . ucfirst($this->fieldName);
        $brickContainer = $element->$containerGetter();

        if (!$brickContainer instanceof ObjectbrickContainer) {
            throw new InvalidConfigurationException(
                sprintf('Field "%s" is not an object brick container.', $this->fieldName)
            );
        }

        if (!in_array($brickType, $brickContainer->getAllowedBrickTypes(), true)) {
            throw new InvalidConfigurationException(
                sprintf('Brick type "%s" is not allowed on field "%s".', $brickType, $this->fieldName)
            );
        }

        if ($this->removeOtherTypes) {
            foreach ($brickContainer->getItems() as $item) {
                if ($item->getType() !== $brickType) {
                    $item->setDoDelete(true);
                }
            }
        }

        $brickGetter = 'get' . $brickType;
        $existingBrick = $brickContainer->$brickGetter();
        if ($existingBrick !== null && !$existingBrick->getDoDelete()) {
            return;
        }

        $brickClassName = '\\Pimcore\\Model\\DataObject\\Objectbrick\\Data\\' . $brickType;
        $brick = new $brickClassName($element);
        $brick->setFieldname($this->fieldName);
        $brickSetter = 'set' . $brickType;
        $brickContainer->$brickSetter($brick);
    }
}
