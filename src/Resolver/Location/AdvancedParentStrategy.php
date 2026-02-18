<?php



namespace TorqIT\DataImporterExtensionsBundle\Resolver\Location;

use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Bundle\DataImporterBundle\Tool\DataObjectLoader;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Service;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Bundle\DataImporterBundle\Resolver\Location\LocationStrategyInterface;
use Pimcore\Model\Element\Service as ElementService;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\PersistingStoreInterface;
use TorqIT\DataImporterExtensionsBundle\Helper\AdvancedPathBuilder;

class AdvancedParentStrategy implements LocationStrategyInterface
{
    /**
     * @var string
     */
    protected $advancedParent;

    /**
     * @var string
     */
    protected $fallbackPath;

    private const LOCK_PREFIX = 'data-importer-extensions-advanced-parent-';


    /**
     * @param DataObjectLoader $dataObjectLoader
     */
    public function __construct(protected DataObjectLoader $dataObjectLoader, private LockFactory $lockFactory, private PersistingStoreInterface $lockStore)
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
        $newParent = null;

        $path = AdvancedPathBuilder::buildPath($inputData, $this->advancedParent);

        $newParent = $this->dataObjectLoader->loadByPath($path);

        if (!($newParent instanceof DataObject) && $path) {
            $lock = $this->lockFactory->createLock($this::LOCK_PREFIX . $path);
            if($lock->acquire(true)){
                try{
                    Service::createFolderByPath($path);
                }
                catch(\Exception){}
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
