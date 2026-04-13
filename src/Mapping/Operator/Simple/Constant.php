<?php

namespace TorqIT\DataImporterExtensionsBundle\Mapping\Operator\Simple;


use Pimcore\Bundle\DataImporterBundle\Mapping\Operator\AbstractOperator;
use Pimcore\Model\Element\Service as ElementService;
use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'pimcore.datahub.data_importer.operator', attributes: ['type' => 'constant'])]
class Constant extends AbstractOperator
{
    protected string $constant;

    public function setSettings(array $settings): void
    {
        $this->constant = $settings['constant'] ?? '';
    }

    public function process($inputData, bool $dryRun = false)
    {
        return $this->constant;
    }

    public function evaluateReturnType(string $inputType, int $index = null): string
    {
        return $inputType;
    }
}