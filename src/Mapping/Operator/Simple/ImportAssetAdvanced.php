<?php

namespace TorqIT\DataImporterExtensionsBundle\Mapping\Operator\Simple;

use Pimcore\Bundle\DataImporterBundle\Mapping\Operator\Simple\ImportAsset;
use Pimcore\Model\Element\DuplicateFullPathException;
use TorqIT\DataImporterExtensionsBundle\Helper\AdvancedPathBuilder;

class ImportAssetAdvanced extends ImportAsset 
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $urlPropertyName;

    /**
     * @param mixed $inputData
     * @param bool $dryRun
     *
     * @return array|false|mixed|null
     *
     * @throws DuplicateFullPathException
     */
    public function process($inputData, bool $dryRun = false)
    {
        
        if(is_string($inputData)){
            $inputData = [$inputData];
        }
        
        $this->parentFolderPath = AdvancedPathBuilder::buildPath($inputData, $this->path, 'asset');

        $rawUrl = array_reverse($inputData)[0];
        $assetReturn = parent::process($rawUrl, $dryRun);

        if($assetReturn instanceof \Pimcore\Model\Asset\Image){
            $assetReturn->setProperty($this->urlPropertyName, "text",  $rawUrl);
            $assetReturn->save();
        }

        return $assetReturn;
    }
     
    
    /**
     * @var string
     */
    protected $constant;

    public function setSettings(array $settings): void
    {
        parent::setSettings($settings);

        $this->path = $settings['path'] ?? '';
        $this->urlPropertyName = $settings["urlPropertyName"] ?? null;
    }

}