<?php

namespace TorqIT\DataImporterExtensionsBundle\Mapping\Operator\Simple;


use Pimcore\Bundle\DataImporterBundle\Mapping\Operator\AbstractOperator;
use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;

enum ArithmeticOperators: string
{
    case Addition = "Addition";
    case Subtraction = "Subtraction";
    case Multiplication = "Multiplication";
    case Division = "Division";
}

class Arithmetic extends AbstractOperator
{
    /**
     * @var string
     */
    protected $arithmeticOperator;

    /**
     * @var numeric
     */
    protected $staticNumber;

    public function setSettings(array $settings): void
    {
        $this->arithmeticOperator = $settings['arithmeticOperator'] ?? ArithmeticOperators::Addition->value;

        $this->staticNumber = $settings['staticNumber'] ?? ($this->arithmeticOperator == ArithmeticOperators::Division->value ? 1 : 0);
    }

    public function process($inputData, bool $dryRun = false)
    {
        if (!$num = floatval($inputData)) {
            throw new InvalidConfigurationException("Input must be a numeric type!");
        }
        switch ($this->arithmeticOperator) {
            case ArithmeticOperators::Addition->value:
                return $num + $this->staticNumber;
            case ArithmeticOperators::Subtraction->value:
                return $num - $this->staticNumber;
            case ArithmeticOperators::Multiplication->value:
                return $num * $this->staticNumber;
            case ArithmeticOperators::Division->value:
                return $num / $this->staticNumber;
            default:
                throw new InvalidConfigurationException("Arithmetic operator not valid");
        }
    }

    public function evaluateReturnType(string $inputType, int $index = null): string
    {
        if ($inputType == "numeric") {
            return "numeric";
        }
    }
}
