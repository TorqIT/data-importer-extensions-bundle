<?php
namespace TorqIT\DataImporterExtensionsBundle\EventListener;

use Pimcore\Bundle\DataImporterBundle\Event\DataObject\PreSaveEvent;
use Pimcore\Bundle\DataImporterBundle\Settings\ConfigurationPreparationService;
use Pimcore\Model\DataObject\AutomotiveProduct;
use Pimcore\Model\DataObject\Brand;
use Carbon\Carbon;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Classificationstore;
use Pimcore\Model\DataObject\Classificationstore\StoreConfig;
use Pimcore\Model\DataObject\Classificationstore\GroupConfig;
use Pimcore\Model\DataObject\Classificationstore\KeyConfig;
use Pimcore\Model\DataObject\Classificationstore\KeyGroupRelation;
use Pimcore\Model\DataObject\ClassDefinition\Data\Input;
use Pimcore\Model\DataObject\Data\Hotspotimage;
use Pimcore\Model\DataObject\Data\ImageGallery;
use Pimcore\Model\DataObject\Data\QuantityValue;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\Packaging;

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

        if(isset($data['MinimumOrderQuantity']['value'])){
            $object->setMinimumOrderQuantity($data['MinimumOrderQuantity']['value']);
        }

        if(isset($data['QuantityPerApplication']['value'])){
            $object->setQuantityPerApplication($data['QuantityPerApplication']['value']);
        }

        if(isset($data['QuantityPerApplication']['value'])){
            $object->setQuantityPerApplication($data['QuantityPerApplication']['value']);
        }

        if(isset($data['BrandAAIAID'])){
            $brand = Brand::getByAAIAID($data['BrandAAIAID'], 1);
            if($brand){
                $object->setBrand($brand);
            }
        }

        $this->setDescriptions($object, $data);
        $this->setPrices($object, $data);
        $this->setImages($object, $data);
        $this->setExtendedAttribtues($object, $data);
        $this->setPackaging($object, $data);
        $this->setProductAttributes($object, $data);

        $object->setHazardousMaterialCode($data['HazardousMaterialCode'] == 'Y' ? true : false);
    }

    private function setDescriptions(AutomotiveProduct $product, $data){

        if(!isset($data['Descriptions']['Description'])){
            return;
        }

        $product->setInstructionalDescription('');

        foreach($data['Descriptions']['Description'] as $description){
            if(isset($description['LanguageCode']) && $description['LanguageCode'] !="EN"){
                continue;
            }
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
                case 'ASC':
                    $product->setInstructionalDescription($value . "\n" . $product->getInstructionalDescription() );
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

    private function setExtendedAttribtues(AutomotiveProduct $product, $data){
        if(!isset($data['ExtendedInformation']['ExtendedProductInformation'])){
            return;
        }

        foreach($data['ExtendedInformation']['ExtendedProductInformation'] as $ei){
            $value = $ei['value'];
            switch($ei['EXPICode']){

                case 'CTO':
                    $product->setCountryOfOrigin($value);
                    break;
                case 'HSB':
                    $product->setHSBCode($value);
                    break;
                case 'HTS':
                    $product->setHTSCode($value);
                    break;
                case 'TAX':
                    $product->setTaxable($value === 'Y');
                    break;
                case 'WT1':
                    $product->setWarrantyMonths($value);
                    break;
            }
        }
    }

    private function setPackaging(AutomotiveProduct $product, $data){
        if(!isset($data['Packages']['Package'])){
            return;
        }

        $iterate = $data['Packages']['Package'];

        if(isset($data['Packages']['Package']['MaintenanceType'])){
            $iterate = $data['Packages'];
        }

        foreach($iterate as $package){
            if($package['PackageUOM'] != 'EA'){
                continue;
            }

            $fieldCollection = $product->getPackagingEach() ?? new Fieldcollection();
            $packaging =  $fieldCollection->getItems()[0] ?? new Packaging();

            if(isset($package['PackageLevelGTIN'])){
                $packaging->setGTIN($package['PackageLevelGTIN']);
            }
            
            $packaging->setUOM($package['PackageUOM']);
            $packaging->setQuantityOfEaches($package['QuantityofEaches']);

            if(isset($package['Dimensions']['MerchandisingHeight'])){
                $packaging->setMerchandisingHeight(new QuantityValue($package['Dimensions']['MerchandisingHeight'], $package['Dimensions']['UOM']));
            }
            if(isset($package['Dimensions']['MerchandisingWidth'])){
                $packaging->setMerchandisingWidth(new QuantityValue($package['Dimensions']['MerchandisingWidth'], $package['Dimensions']['UOM']));
            }
            if(isset($package['Dimensions']['MerchandisingLength'])){
                $packaging->setMerchandisingLength(new QuantityValue($package['Dimensions']['MerchandisingLength'], $package['Dimensions']['UOM']));
            }
            if(isset($package['Dimensions']['Height'])){
                $packaging->setMerchandisingHeight(new QuantityValue($package['Dimensions']['Height'], $package['Dimensions']['UOM']));
            }
            if(isset($package['Dimensions']['Width'])){
                $packaging->setMerchandisingWidth(new QuantityValue($package['Dimensions']['Width'], $package['Dimensions']['UOM']));
            }
            if(isset($package['Dimensions']['Length'])){
                $packaging->setMerchandisingLength(new QuantityValue($package['Dimensions']['Length'], $package['Dimensions']['UOM']));
            }
            if(isset($package['Dimensions']['ShippingHeight'])){
                $packaging->setShippingHeight(new QuantityValue($package['Dimensions']['ShippingHeight'], $package['Dimensions']['UOM']));
            }
            if(isset($package['Dimensions']['ShippingWidth'])){
                $packaging->setShippingWidth(new QuantityValue($package['Dimensions']['ShippingWidth'], $package['Dimensions']['UOM']));
            }
            if(isset($package['Dimensions']['ShippingLength'])){
                $packaging->setShippingLength(new QuantityValue($package['Dimensions']['ShippingLength'], $package['Dimensions']['UOM']));
            }
            if(isset($package['Weights']['Weight'])){
                $packaging->setWeight($package['Weights']['Weight']);
            }
            if(isset($package['Weights']['DimensionalWeight'])){
                $packaging->setDimensionalWeight($package['Weights']['DimensionalWeight']);
            }

            
            

            $fieldCollection->setItems([$packaging]);
            $product->setPackagingEach($fieldCollection);

        }
    }

    private function setProductAttributes(AutomotiveProduct $product, $data){
        if(!isset($data['ProductAttributes']['ProductAttribute'])){
            return;
        }

        $store = StoreConfig::getByName('ProductAttributes');

        if(!$store){
            $store = new StoreConfig();
            $store->setName('ProductAttributes');
            $store->save();
        }

        $group = GroupConfig::getByName($data['PartTerminologyID'], $store->getId());

        if(!$group){
            $group = new GroupConfig();
            $group->setName($data['PartTerminologyID']);
            $group->setStoreId($store->getId());
            $group->save();
        }

        $iterate = $data['ProductAttributes']['ProductAttribute'];

        if(isset($data['ProductAttributes']['ProductAttribute']['MaintenanceType'])){
            $iterate = $data['ProductAttributes'];
        }

        $cs = $product->getProductAttributes() ?? new Classificationstore();

        $cs->setActiveGroups([$group->getId() => true]);
        

        foreach($iterate as $productAttribute){
            $attributeName = str_replace(' ', '', $productAttribute['AttributeID'] );

            $key = KeyConfig::getByName($attributeName, $store->getId());

            if(!$key){

                $definition = new Input();
                $definition->setName($attributeName);
                $definition->setTitle($productAttribute['AttributeID']);

                $key = new KeyConfig();
                $key->setStoreId($store->getId());
                $key->setName($attributeName);
                $key->setEnabled(true);
                $key->setDescription($productAttribute['AttributeID']);
                $key->setDefinition(json_encode($definition));
                $key->setType('input');
                $key->save();
            }
           
            $keyGroupRelation = KeyGroupRelation::getByGroupAndKeyId($group->getId(), $key->getId());

            if(!$keyGroupRelation){
                $keyGroupRelation = new KeyGroupRelation();
                $keyGroupRelation->setGroupId($group->getId());
                $keyGroupRelation->setKeyId($key->getId());
                $keyGroupRelation->save();
            }

            $cs->setLocalizedKeyValue($group->getId(), $key->getId(), $productAttribute['value'] );
        }

        $product->setProductAttributes($cs);

    }
}