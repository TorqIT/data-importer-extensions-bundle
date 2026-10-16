<?php

namespace TorqIT\DataImporterExtensionsBundle\Mapping\Operator\Simple;


use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Bundle\DataImporterBundle\Mapping\Operator\AbstractOperator;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

enum ArithmeticOperators: string
{
    case Addition = "Addition";
    case Subtraction = "Subtraction";
    case Multiplication = "Multiplication";
    case Division = "Division";
}

#[AutoconfigureTag(name: 'pimcore.datahub.data_importer.operator', attributes: ['type' => 'arithmetic'])]
class Arithmetic extends AbstractOperator
{
    protected string $arithmeticOperator;
    protected int|float $staticNumber;

    public function setSettings(array $settings): void
    {
        $this->arithmeticOperator = $settings['arithmeticOperator'] ?? ArithmeticOperators::Addition->value;
        $this->staticNumber = $settings['staticNumber'] ?? ($this->arithmeticOperator == ArithmeticOperators::Division->value ? 1 : 0);
    }

    public function process($inputData, bool $dryRun = false)
    {
        if (!is_numeric($inputData)) {
            throw new InvalidConfigurationException("Input must be a numeric type!");
        }
        $num = floatval($inputData);
        return match ($this->arithmeticOperator) {
            ArithmeticOperators::Addition->value => $num + $this->staticNumber,
            ArithmeticOperators::Subtraction->value => $num - $this->staticNumber,
            ArithmeticOperators::Multiplication->value => $num * $this->staticNumber,
            ArithmeticOperators::Division->value => $num / $this->staticNumber,
            default => throw new InvalidConfigurationException("Arithmetic operator not valid"),
        };
    }

    public function evaluateReturnType(string $inputType, int $index = null): string
    {
        if ($inputType == "numeric") {
            return "numeric";
        }
    }
}
