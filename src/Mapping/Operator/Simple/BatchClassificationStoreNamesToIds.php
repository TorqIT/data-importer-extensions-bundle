<?php

namespace TorqIT\DataImporterExtensionsBundle\Mapping\Operator\Simple;


use Doctrine\DBAL\Connection;
use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Bundle\DataImporterBundle\Mapping\Operator\AbstractOperator;
use Pimcore\Log\ApplicationLogger;
use Torq\PimcoreHelpersBundle\Service\Utility\ArrayUtils;

class BatchClassificationStoreNamesToIds extends AbstractOperator
{
    public function __construct(
        ApplicationLogger $applicationLogger,
        private Connection $connection,
        private ArrayUtils $utils
    ) {
        parent::__construct($applicationLogger);
    }

    public function setSettings(array $settings): void
    {
    }

    public function process($inputData, bool $dryRun = false)
    {
        if (!is_array($inputData)) {
            throw new InvalidConfigurationException("Input must be an array!");
        }
        if (count($inputData) > 0 && count($inputData[0]) !== 3) {
            throw new InvalidConfigurationException(
                "Classification store entry arrays must have 3 elements in the following order: group, key, value"
            );
        }
        if (count($inputData) === 0) {
            return $inputData;
        }

        $qb = $this->connection->createQueryBuilder();
        $qb->select('csg.id as groupId', 'csg.name as groupName', 'csk.id as keyId', 'csk.name as keyName');
        $qb->from('classificationstore_relations', 'r');
        $qb->join('r', 'classificationstore_groups', 'csg', 'r.groupId = csg.id');
        $qb->join('r', 'classificationstore_keys', 'csk', 'r.keyId = csk.id');

        $expressions = [];
        foreach ($inputData as $i => $datum) {
            [$groupName, $keyName] = array_values($datum);
            $expressions[] = $qb->expr()->and(
                $qb->expr()->eq('csg.name', ":csgName$i"),
                $qb->expr()->eq('csk.name', ":cskName$i"),
            );
            $qb->setParameter("csgName$i", $groupName);
            $qb->setParameter("cskName$i", $keyName);
        }
        $qb->where($qb->expr()->or(...$expressions));
        $groupKeyQuartets = $qb->executeQuery()->fetchAllAssociative();

        $remapped = [];
        foreach ($inputData as $attribute) {
            [$group, $key, $value] = array_values($attribute);
            $matchingQuartet = $this->utils->findInArray(
                fn(array $quartet) => $group === $quartet['groupName'] && $key === $quartet['keyName'],
                $groupKeyQuartets
            );
            if ($matchingQuartet) {
                ['groupId' => $groupId, 'keyId' => $keyId] = $matchingQuartet;
                $remapped["$groupId-$keyId"] = $value;
            } else {
                throw new InvalidConfigurationException(
                    "No group & key pair found for group: `$group` and key: `$key`"
                );
            }
        }
        return $remapped;
    }

    public function evaluateReturnType(string $inputType, int $index = null): string
    {
        return 'array';
    }
}