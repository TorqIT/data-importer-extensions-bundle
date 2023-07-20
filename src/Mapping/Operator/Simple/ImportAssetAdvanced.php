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
     * @param mixed $inputData
     * @param bool $dryRun
     *
     * @return array|false|mixed|null
     *
     * @throws DuplicateFullPathException
     */
    public function process($inputData, bool $dryRun = false)
    {
        $this->parentFolderPath = AdvancedPathBuilder::buildPath($inputData, $this->path, 'asset');

        return parent::process(array_reverse($inputData)[0], $dryRun);
    }
     
    
    /**
     * @var string
     */
    protected $constant;

    public function setSettings(array $settings): void
    {
        parent::setSettings($settings);

        $this->path = $settings['path'] ?? '';
    }

}