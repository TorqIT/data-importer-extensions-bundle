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
use Pimcore\Model\DataObject\AutomotiveProduct;
use Carbon\Carbon;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Data\Hotspotimage;
use Pimcore\Model\DataObject\Data\ImageGallery;
use Pimcore\Model\DataObject\Data\QuantityValue;

class PiesSaveListener {
     
    public function piesSave (PreSaveEvent $e) {
       
        $config = (new ConfigurationPreparationService())->prepareConfiguration($e->getConfigName());
        
        if($config['interpreterConfig']['type'] != 'pies') {
            return;
        }

        /** @var AutomotiveProduct $object */
        $object = $e->getDataObject();

        $data = $e->getRawData();
        
        $object->setKey($data['PartNumber']);
        $object->setPartNumber($data['PartNumber']);

        //php check if array key exists


        if(isset($data['ItemEffectiveDate'])){
            $object->setItemEffectiveDate(new Carbon($data['ItemEffectiveDate']));
        }

        if(isset($data['ItemLevelGTIN']['value'])){
            $object->setItemLevelGTIN($data['ItemLevelGTIN']['value']);
        }

        if(isset($data['PartTerminologyID'])){
            $object->setPartTerminologyID($data['PartTerminologyID']);
        }

        $this->setDescriptions($object, $data);
        $this->setPrices($object, $data);
        $this->setImages($object, $data);

        $object->setHazardousMaterialCode($data['HazardousMaterialCode'] == 'Y' ? true : false);
    }

    private function setDescriptions(AutomotiveProduct $product, $data){

        if(!isset($data['Descriptions']['Description'])){
            return;
        }

        foreach($data['Descriptions']['Description'] as $description){
            $value = $description['value'];
            switch($description['DescriptionCode']){

                case 'ASM':
                    $product->setFitmentDescription($value);
                    break;
                case 'DES':
                    $product->setShortDescription($value);
                    break;
                case 'SHO':
                    $product->setProductType($value);
                    break;
                case 'MKT':
                    $product->setMarketingDescription($value);
                    break;
            }
        }
    }

    private function setPrices(AutomotiveProduct $product, $data){

        if(!isset($data['Prices']['Pricing'])){
            return;
        }

        foreach($data['Prices']['Pricing'] as $price){
            $value = $price['Price']['value'];
            switch($price['PriceType']){

                case 'JBR':
                    $product->setCost(new QuantityValue($value, 'USD'));
                    break;
                case 'LST':
                    $product->setListing(new QuantityValue($value, 'USD'));
                    break;
                case 'RET':
                    $product->setRetail(new QuantityValue($value, 'USD'));
                    break;
            }
        }
    }

    private function setImages(AutomotiveProduct $product, $data){
        if(!isset($data['DigitalAssets']['DigitalFileInformation'])){
            return;
        }

        $imageGallery = $product->getImages() ?? new ImageGallery();
        $galleryItems = $imageGallery->getItems() ?? [];


        $iterate = $data['DigitalAssets']['DigitalFileInformation'];

        if(isset($data['DigitalAssets']['DigitalFileInformation']['FileType'])){
            $iterate = $data['DigitalAssets'];
        }
        
        foreach($iterate as $digitalAsset){
            $localAsset = Asset::getByPath(sprintf('/PIES/%s/Assets/%s',$data['BrandAAIAID'], $digitalAsset['FileName']));

            if(!$localAsset || $digitalAsset['FileType'] != 'JPG'){
                continue;
            }

            $exists = false;
            foreach($galleryItems as $galleryItem){
                if($galleryItem->getImage()->getId() == $localAsset->getId()){
                    $exists = true;
                    break;
                }
            }

            if(!$exists){
                $galleryItems[] = new Hotspotimage($localAsset);
            }
        }

        $imageGallery->setItems($galleryItems);
        $product->setImages($imageGallery);
    }

}