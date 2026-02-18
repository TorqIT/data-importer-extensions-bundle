<?php



namespace TorqIT\DataImporterExtensionsBundle\Resolver\Load;

use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Element\Service as ElementService;
use Pimcore\Model\Property\Dao;

class PropertyStrategy extends \Pimcore\Bundle\DataImporterBundle\Resolver\Load\AbstractLoad
{
    
    private string $propertyName;

    private string $valueIndex;

    /**
     * @param array $inputData
     *
     * @return ElementInterface|null
     *
     * @throws InvalidConfigurationException
     */
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
            throw new \Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException('Empty propertyName.');
        }

        $this->propertyName = $settings['propertyName'];
        $this->valueIndex = $settings['valueIndex'];
    }

    /**
     * @param string $identifier
     *
     * @return ElementInterface|null
     *
     * @throws InvalidConfigurationException
     */
    public function loadElementByIdentifier($identifier): ?ElementInterface
    {
        return null;
    }

    /**
     * @return array
     */
    public function loadFullIdentifierList(): array
    {
        return array();
    }
}