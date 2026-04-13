<?php

namespace TorqIT\DataImporterExtensionsBundle\Mapping\Operator\Simple;

use Pimcore\Bundle\DataImporterBundle\Mapping\Operator\Simple\ImportAsset;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\Element\DuplicateFullPathException;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use TorqIT\DataImporterExtensionsBundle\Helper\AdvancedPathBuilder;

#[AutoconfigureTag(name: 'pimcore.datahub.data_importer.operator', attributes: ['type' => 'importAssetAdvanced'])]
class ImportAssetAdvanced extends ImportAsset 
{
    protected string $path;
    protected string $urlPropertyName;
    protected string $constant;

    /**
     * @return array|false|mixed|null
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

        if ($assetReturn instanceof Image) {
            $assetReturn->setProperty($this->urlPropertyName, "text",  $rawUrl);
            $assetReturn->save();
        }

        return $assetReturn;
    }

    public function setSettings(array $settings): void
    {
        parent::setSettings($settings);

        $this->path = $settings['path'] ?? '';
        $this->urlPropertyName = $settings["urlPropertyName"] ?? null;
    }

}