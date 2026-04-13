<?php



namespace TorqIT\DataImporterExtensionsBundle\Mapping\DataTarget;

use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Bundle\DataImporterBundle\Mapping\DataTarget\Classificationstore as ClassificationStoreDataTarget;
use Pimcore\Model\DataObject\Classificationstore;
use Pimcore\Model\DataObject\Data\QuantityValue;
use Pimcore\Model\Element\ElementInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'pimcore.datahub.data_importer.data_target', attributes: ['type' => 'advancedClassificationStore'])]
class AdvancedClassificationStore extends ClassificationStoreDataTarget
{
    protected bool $writeIfSourceIsEmpty;
    protected bool $writeIfTargetIsNotEmpty;

    /** @throws InvalidConfigurationException */
    public function setSettings(array $settings): void
    {
        parent::setSettings($settings);
        $this->writeIfSourceIsEmpty = $settings['writeIfSourceIsEmpty'] ?? true;
        $this->writeIfTargetIsNotEmpty = $settings['writeIfTargetIsNotEmpty'] ?? true;
    }

    /** @throws InvalidConfigurationException */
    public function assignData(ElementInterface $element, $data)
    {
        $getter = 'get' . ucfirst($this->fieldName);
        $classificationStore = $element->$getter();

        if (!($classificationStore instanceof Classificationstore)) {
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
