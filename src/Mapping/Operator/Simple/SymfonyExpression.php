<?php

declare(strict_types=1);

namespace TorqIT\DataImporterExtensionsBundle\Mapping\Operator\Simple;

use Pimcore\Bundle\DataImporterBundle\Mapping\Operator\AbstractOperator;
use Pimcore\Bundle\DataImporterBundle\Mapping\Type\TransformationDataTypeService;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Contracts\Service\Attribute\Required;
use Torq\PimcoreHelpersBundle\Service\Common\DataImporterExpressionLanguage;

#[AutoconfigureTag(name: 'pimcore.datahub.data_importer.operator', attributes: ['type' => 'symfonyExpression'])]
class SymfonyExpression extends AbstractOperator
{
    protected string $expression = '';

    private DataImporterExpressionLanguage $expressionLanguage;

    #[Required]
    public function setExpressionLanguage(DataImporterExpressionLanguage $expressionLanguage): void
    {
        $this->expressionLanguage = $expressionLanguage;
    }

    public function setSettings(array $settings): void
    {
        $this->expression = trim($settings['expression'] ?? '');
    }

    public function process($inputData, bool $dryRun = false): mixed
    {
        if (empty($this->expression)) {
            return $inputData;
        }

        return $this->expressionLanguage->evaluate($this->expression, ['attributes' => $inputData]);
    }

    public function evaluateReturnType(string $inputType, ?int $index = null): string
    {
        return TransformationDataTypeService::DEFAULT_TYPE;
    }
}
