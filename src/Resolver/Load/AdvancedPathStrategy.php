<?php



namespace TorqIT\DataImporterExtensionsBundle\Resolver\Load;

use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Bundle\DataImporterBundle\Resolver\Load\AbstractLoad;
use Pimcore\Model\Element\ElementInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use TorqIT\DataImporterExtensionsBundle\Helper\AdvancedPathBuilder;

#[AutoconfigureTag(name: 'pimcore.datahub.data_importer.resolver.load', attributes: ['type' => 'advancedPath'])]
class AdvancedPathStrategy extends AbstractLoad
{
    private string $advancedPath;

    /** @throws InvalidConfigurationException */
    public function loadElement(array $inputData): ?ElementInterface
    {
        $path = AdvancedPathBuilder::buildPath($inputData, $this->advancedPath);
        return $this->dataObjectLoader->loadByPath($path, $this->getClassName());
    }

    /**
     * @param string $identifier
     * @throws InvalidConfigurationException
     */
    public function loadElementByIdentifier($identifier): ?ElementInterface
    {
        return $this->dataObjectLoader->loadByPath($identifier, $this->getClassName());
    }

    public function loadFullIdentifierList(): array
    {
        $sql = sprintf('SELECT CONCAT(`o_path`, `o_key`) FROM object_%s', $this->dataObjectClassId);
        return $this->db->fetchCol($sql);
    }

    public function setSettings(array $settings): void
    {
        if (!array_key_exists('advancedPath', $settings) || $settings['advancedPath'] === null) {
            throw new InvalidConfigurationException('Empty advanced path.');
        }
        $this->advancedPath = $settings['advancedPath'];
    }
}
