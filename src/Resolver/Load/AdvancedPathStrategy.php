<?php



namespace TorqIT\DataImporterExtensionsBundle\Resolver\Load;

use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Element\Service as ElementService;
use TorqIT\DataImporterExtensionsBundle\Helper\AdvancedPathBuilder;

class AdvancedPathStrategy extends \Pimcore\Bundle\DataImporterBundle\Resolver\Load\AbstractLoad
{
    
    private string $advancedPath;

    /**
     * @param array $inputData
     *
     * @return ElementInterface|null
     *
     * @throws InvalidConfigurationException
     */
    public function loadElement(array $inputData): ?ElementInterface
    {
        $path = AdvancedPathBuilder::buildPath($inputData, $this->advancedPath);

        return $this->dataObjectLoader->loadByPath($path, $this->getClassName());
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
        return $this->dataObjectLoader->loadByPath($identifier,
                                                   $this->getClassName());
    }

    /**
     * @return array
     */
    public function loadFullIdentifierList(): array
    {
        $sql = sprintf('SELECT CONCAT(`o_path`, `o_key`) FROM object_%s', $this->dataObjectClassId);

        return $this->db->fetchCol($sql);
    }


    public function setSettings(array $settings): void
    {
        if (!array_key_exists('advancedPath', $settings) || $settings['advancedPath'] === null) {
            throw new \Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException('Empty advanced path.');
        }

        $this->advancedPath = $settings['advancedPath'];
    }
}
