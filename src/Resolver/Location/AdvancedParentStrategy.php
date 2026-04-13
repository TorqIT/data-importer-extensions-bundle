<?php



namespace TorqIT\DataImporterExtensionsBundle\Resolver\Location;

use Exception;
use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Bundle\DataImporterBundle\Resolver\Location\LocationStrategyInterface;
use Pimcore\Bundle\DataImporterBundle\Tool\DataObjectLoader;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Service;
use Pimcore\Model\Element\ElementInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Lock\LockFactory;
use TorqIT\DataImporterExtensionsBundle\Helper\AdvancedPathBuilder;

#[AutoconfigureTag(name: 'pimcore.datahub.data_importer.resolver.location', attributes: ['type' => 'advancedParent'])]
class AdvancedParentStrategy implements LocationStrategyInterface
{
    private const string LOCK_PREFIX = 'data-importer-extensions-advanced-parent-';

    protected string $advancedParent;
    protected string $fallbackPath;

    public function __construct(
        protected DataObjectLoader $dataObjectLoader,
        private LockFactory $lockFactory
    )
    {
    }

    public function setSettings(array $settings): void
    {
        if (empty($settings['advancedParent'])) {
            throw new InvalidConfigurationException('No advanced parent');
        }

        $this->advancedParent = $settings['advancedParent'];
        $this->fallbackPath = $settings['fallbackPath'] ?? null;
    }

    public function updateParent(ElementInterface $element, array $inputData): ElementInterface
    {
        $path = AdvancedPathBuilder::buildPath($inputData, $this->advancedParent);
        $newParent = $this->dataObjectLoader->loadByPath($path);
        if (!($newParent instanceof DataObject) && $path) {
            $lock = $this->lockFactory->createLock($this::LOCK_PREFIX . $path);
            if($lock->acquire(true)){
                try{
                    Service::createFolderByPath($path);
                } catch (Exception) {
                }
                $lock->release();
            }
            $newParent = $this->dataObjectLoader->loadByPath($path);
        }

        if (!($newParent instanceof DataObject) && $this->fallbackPath) {
            $newParent = DataObject::getByPath($this->fallbackPath);
        }

        if ($newParent) {
            return $element->setParent($newParent);
        }

        return $element;
    }

}
