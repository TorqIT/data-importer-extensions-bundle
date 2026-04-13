<?php



namespace TorqIT\DataImporterExtensionsBundle\Mapping\DataTarget;

use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Bundle\DataImporterBundle\Mapping\DataTarget\Direct;
use Pimcore\Model\DataObject\Data\ImageGallery;
use Pimcore\Model\Element\ElementInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'pimcore.datahub.data_importer.data_target', attributes: ['type' => 'imageGalleryAppender'])]
class ImageGalleryAppender extends Direct
{
    protected bool $includeDuplicates;

    /**
     * @param ImageGallery $data
     * @throws InvalidConfigurationException
     */
    public function assignData(ElementInterface $element, $data): void
    {
        if (empty($data->getItems())) {
            return;
        }

        $setterParts = explode('.', $this->fieldName);
        if (count($setterParts) === 1) {
            //direct class attribute
            $getter = 'get' . ucfirst($this->fieldName);
            $valueContainer = $element;

        } elseif (count($setterParts) === 3) {
            //brick attribute
            $brickContainerGetter = 'get' . ucfirst($setterParts[0]);
            $brickContainer = $element->$brickContainerGetter();
            $brickGetter = 'get' . ucfirst($setterParts[1]);
            $brick = $brickContainer->$brickGetter();
            if (empty($brick)) {
                $brickClassName = '\\Pimcore\\Model\\DataObject\\Objectbrick\\Data\\' . ucfirst($setterParts[1]);
                $brick = new $brickClassName($element);
                $brickSetter = 'set' . ucfirst($setterParts[1]);
                $brickContainer->$brickSetter($brick);
            }

            $getter = 'get' . ucfirst($setterParts[2]);

            $valueContainer = $brick;
        } else {
            throw new InvalidConfigurationException('Invalid number of setter parts for ' . $this->fieldName);
        }

        /** @var ImageGallery $gallery */
        $gallery = $valueContainer->$getter();

        if (!$gallery) {
            $gallery = $data;
        }
        else{
            $galleryItems = $gallery->getItems();
            $newImage = $data->getItems()[0];

            if(!$this->includeDuplicates) {
                foreach($galleryItems as $galleryItem) {
                    if($galleryItem->getImage()->getId() == $newImage->getImage()->getId()) {
                        return;
                    }
                }
            } 
           
            $galleryItems[] = $newImage;
            $gallery->setItems($galleryItems);
        }

        parent::assignData($element, $gallery);
    }

    /** @throws InvalidConfigurationException */
    public function setSettings(array $settings): void
    {
        parent::setSettings($settings);
    }
}
