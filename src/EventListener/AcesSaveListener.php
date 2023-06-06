<?php
namespace TorqIT\DataImporterExtensionsBundle\EventListener;

use Pimcore\Bundle\DataImporterBundle\Event\DataObject\PreSaveEvent;
use Pimcore\Bundle\DataImporterBundle\Settings\ConfigurationPreparationService;
use Pimcore\Model\DataObject\Vehicle;
use Pimcore\Model\DataObject\AutomotiveProduct;
use Pimcore\Model\DataObject;

class AcesSaveListener {
     
    public function acesSave (PreSaveEvent $e) {
       
        $config = (new ConfigurationPreparationService())->prepareConfiguration($e->getConfigName());
        
        if($config['interpreterConfig']['type'] != 'aces') {
            return;
        }

        /** @var Vehicle $object */
        $object = $e->getDataObject();

        $data = $e->getRawData();

        /** @var AutomotiveProduct\Listing $automotiveListing */
        $automotiveListing = new AutomotiveProduct\Listing();

        $automotiveListing->setLimit(1);
        $automotiveListing->setCondition("PartNumber = ?", $data["Part"]);

        $list = $automotiveListing->load();

        if(count($list) == 0){
            return;
        }

        /** @var AutomotiveProduct $part */

        $part = $list[0];

        $fitments = $object->getPartFitment();

        $existingFitment = null;

        foreach($fitments as &$fitment){
            if($fitment->getObject()->getPartNumber() == $part->getPartNumber()){
                $existingFitment = &$fitment;
                break;
            }
        }

        if($existingFitment){
            $this->updateFitment($object, $part, $data, $fitment);
        } else{
            $fitments[] = $this->addFitment($object, $part, $data);
        }

        $object->setPartFitment($fitments);
                
    }

    private function addFitment(Vehicle $vehicle, AutomotiveProduct $part, array $data){
        $metadata = new DataObject\Data\ObjectMetadata('PartFitment', ['notes', 'qty', 'partType', 'mfrLabel', 'position'], $part);
        
        if(isset($data['Note'])){
            $metadata->setNotes($this->getNotes($data['Note']));
        }
        
        if(isset($data['Qty'])){
            $metadata->setQty($data['Qty']);
        }

        if(isset($data['PartType']['id'])){
            $metadata->setPartType($data['PartType']['id']);
        }

        if(isset($data['MfrLabel'])){
            $metadata->setMfrLabel($data['MfrLabel']);
        }

        if(isset($data['Position']['id'])){
            $metadata->setPosition($data['Position']['id']);
        }

        return $metadata;
    }

    private function updateFitment(Vehicle $vehicle, AutomotiveProduct $part, array $data, &$fitment){

    }

    private function getNotes($notes){
        return implode(" | ", $notes);
    }

}