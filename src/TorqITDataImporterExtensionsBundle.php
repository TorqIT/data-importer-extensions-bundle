<?php

namespace TorqIT\DataImporterExtensionsBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\PimcoreBundleAdminClassicInterface;
use Torq\PimcoreHelpersBundle\Service\Common\BundleAssetResolverTrait;

class TorqITDataImporterExtensionsBundle extends AbstractPimcoreBundle implements PimcoreBundleAdminClassicInterface
{
    use BundleAssetResolverTrait;

    public function getCssPaths(): array
    {
        return [];
    }

    public function getEditmodeJsPaths(): array
    {
        return [];
    }

    public function getEditmodeCssPaths(): array
    {
        return [];
    }

    public function getJsPaths(): array
    {
        $paths = $this->getBundleAssetPaths($this->getPath() . '/Resources/public/js', 'js');
        return array_map(fn($p) => "/bundles/torqitdataimporterextensions/$p", $paths);
    }
}
