<?php

namespace TorqIT\DataImporterExtensionsBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Torq\PimcoreHelpersBundle\Service\Common\BundleAssetResolverTrait;
use TorqIT\DataImporterExtensionsBundle\DependencyInjection\Compiler\TransformationDataTypeMappingPass;

class TorqITDataImporterExtensionsBundle extends AbstractPimcoreBundle
{
    use BundleAssetResolverTrait;

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new TransformationDataTypeMappingPass());
    }

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
