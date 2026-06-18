<?php

declare(strict_types=1);

namespace TorqIT\DataImporterExtensionsBundle\DependencyInjection\Compiler;

use Pimcore\Bundle\DataImporterBundle\Mapping\Type\TransformationDataTypeService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TransformationDataTypeMappingPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(TransformationDataTypeService::class)) {
            return;
        }

        $definition = $container->findDefinition(TransformationDataTypeService::class);
        $definition->addMethodCall('appendTypeMapping', ['link', 'link']);
        $definition->addMethodCall('appendTypeMapping', ['table', 'table']);
        $definition->addMethodCall('appendTypeMapping', ['structuredTable', 'table']);
    }
}