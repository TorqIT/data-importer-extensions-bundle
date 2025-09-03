<?php

namespace TorqIT\DataImporterExtensionsBundle\Mapping\Operator\Simple;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Bundle\DataImporterBundle\Mapping\Operator\AbstractOperator;
use Pimcore\Log\ApplicationLogger;

class QuantityValueArrayAbbrToId extends AbstractOperator
{
    public function __construct(ApplicationLogger $applicationLogger, private Connection $connection)
    {
        parent::__construct($applicationLogger);
    }

    public function process($inputData, bool $dryRun = false)
    {
        if (!is_array($inputData) || (count($inputData) > 0 && count(array_values($inputData)[0]) !== 2)) {
            throw new InvalidConfigurationException(
                "Input must be an array of length 2 arrays of shape: [value, unit]"
            );
        }

        $qb = $this->connection->createQueryBuilder();
        $qb->select('abbreviation', 'id')->from('quantityvalue_units')->where('abbreviation in (:abbreviations)');
        $qb->setParameter("abbreviations", array_column($inputData, 1), ArrayParameterType::STRING);
        $idLookup = $qb->executeQuery()->fetchAllAssociativeIndexed();
        $data = array_map(fn(array $d) => [$d[0], $idLookup[$d[1]]['id']], $inputData);
        return $data;
    }

    public function evaluateReturnType(string $inputType, ?int $index = null): string
    {
        return 'array';
    }
}