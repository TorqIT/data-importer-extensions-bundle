<?php
namespace TorqIT\DataImporterExtensionsBundle\EventListener;

use Pimcore\Bundle\DataImporterBundle\Event\DataObject\PreSaveEvent;
use Pimcore\Bundle\DataImporterBundle\Settings\ConfigurationPreparationService;
use Pimcore\Event\Model\ElementEventInterface;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Event\Model\AssetEvent;
use Pimcore\Event\Model\DocumentEvent;
use Symfony\Component\EventDispatcher\GenericEvent;
use Pimcore\Model\DataObject\Product\Listing as ProductListing;

class PiesSaveListener {
     
    public function piesSave (PreSaveEvent $e) {
       
        $config = (new ConfigurationPreparationService())->prepareConfiguration($e->getConfigName());
        
        if($config['interpreterConfig']['type'] != 'pies') {
            return;
        }

        $ok = 5;
        $ok++;

        
    }
}