<?php

namespace TorqIT\DataImporterExtensionsBundle\Mapping\Operator\Simple;


use Pimcore\Bundle\DataImporterBundle\Mapping\Operator\AbstractOperator;
use Pimcore\Model\Element\Service as ElementService;
use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;

class Constant extends AbstractOperator
{
    
     /**
     * @var string
     */
    protected $constant;

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