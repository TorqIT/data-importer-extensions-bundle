<?php



namespace TorqIT\DataImporterExtensionsBundle\Resolver\Load;

use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Bundle\DataImporterBundle\Resolver\Load\AbstractLoad;
use Pimcore\Model\Element\ElementInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'pimcore.datahub.data_importer.resolver.load', attributes: ['type' => 'property'])]
class PropertyStrategy extends AbstractLoad
{
    private string $propertyName;
    private string $valueIndex;

    /** @throws InvalidConfigurationException */
    public function loadElement(array $inputData): ?ElementInterface
    {
        $cidResults = $this->db->fetchAllAssociative('SELECT cid FROM properties WHERE name=? AND data=? AND ctype=?', [$this->propertyName, $inputData[$this->valueIndex],'object']);

        if(count($cidResults) == 0){
            return null;
        }

        $cid = $cidResults[0]['cid'];
        return $this->dataObjectLoader->loadById($cid, $this->getClassName());
    }

    public function setSettings(array $settings): void
    {
        if (!array_key_exists('propertyName', $settings) || $settings['propertyName'] === null) {
            throw new InvalidConfigurationException('Empty propertyName.');
        }

        $this->propertyName = $settings['propertyName'];
        $this->valueIndex = $settings['valueIndex'];
    }

    /** @param string $identifier */
    public function loadElementByIdentifier($identifier): ?ElementInterface
    {
        return null;
    }

    public function loadFullIdentifierList(): array
    {
        return array();
    }
}