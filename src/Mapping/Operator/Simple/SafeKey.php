<?php

namespace TorqIT\DataImporterExtensionsBundle\Mapping\Operator\Simple;


use Pimcore\Bundle\DataImporterBundle\Mapping\Operator\AbstractOperator;
use Pimcore\Model\Element\Service as ElementService;
use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'pimcore.datahub.data_importer.operator', attributes: ['type' => 'safeKey'])]
class SafeKey extends AbstractOperator
{
    public function setSettings(array $settings): void
    {
       
    }

    public function process($inputData, bool $dryRun = false)
    {
        if(!is_string($inputData)){
            throw new InvalidConfigurationException("Input must be a string!");
        }
        return ElementService::getValidKey($inputData, 'object');
    }

    public function evaluateReturnType(string $inputType, int $index = null): string
    {
        return $inputType;
    }
}