<?php

namespace TorqIT\DataImporterExtensionsBundle\Mapping\Operator\Simple;

use Pimcore\Bundle\DataImporterBundle\Mapping\Operator\AbstractOperator;
use Pimcore\Log\ApplicationLogger;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'pimcore.datahub.data_importer.operator', attributes: ['type' => 'eachAsArray'])]
class EachAsArray extends AbstractOperator
{
    public function __construct(ApplicationLogger $applicationLogger)
    {
        parent::__construct($applicationLogger);
    }
    
    public function process($inputData, bool $dryRun = false)
    {
        if (is_array($inputData)) {
            return array_map(fn($d) => is_array($d) ? $d : [$d], $inputData);
        } else {
            return $inputData;
        }
    }

    public function evaluateReturnType(string $inputType, ?int $index = null): string
    {
        return 'array';
    }
}