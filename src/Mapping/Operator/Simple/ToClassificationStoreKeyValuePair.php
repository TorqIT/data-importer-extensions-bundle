<?php

declare(strict_types=1);

namespace TorqIT\DataImporterExtensionsBundle\Mapping\Operator\Simple;

use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Bundle\DataImporterBundle\Mapping\Operator\AbstractOperator;
use Pimcore\Bundle\DataImporterBundle\Mapping\Type\TransformationDataTypeService;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Torq\PimcoreHelpersBundle\Repository\GroupRepository;
use Torq\PimcoreHelpersBundle\Repository\KeyRepository;

#[AutoconfigureTag(name: 'pimcore.datahub.data_importer.operator', attributes: ['type' => 'toClassificationStoreKeyValuePair'])]
class ToClassificationStoreKeyValuePair extends AbstractOperator
{
    public function __construct(
        \Pimcore\Log\ApplicationLogger $applicationLogger,
        private GroupRepository $groupRepository,
        private KeyRepository $keyRepository
    ) {
        parent::__construct($applicationLogger);
    }

    protected ?int $storeId = null;

    public function setSettings(array $settings): void
    {
        $this->storeId = key_exists('storeId', $settings) ? intval($settings['storeId']) : null;
    }

    /**
     * Accepts a length-3 array [$group, $key, $value] and returns an associative array
     * of shape ["{$group}-{$key}" => $value] suitable for classification store import.
     * @return array<string, mixed>
     */
    public function process($inputData, bool $dryRun = false): array
    {
        if (!is_array($inputData) || count($inputData) < 3) {
            throw new InvalidConfigurationException(
                'ToClassificationStoreKeyValuePair operator requires an array with at least 3 elements [group, key, ...values].'
            );
        }
        $group = $this->getGroupId($inputData[0], $this->storeId);
        $key = $this->getKeyId($inputData[1], $this->storeId);
        if (count($inputData) === 3) {
            $value = $inputData[2];
        } else {
            $value = array_slice($inputData, 2);
        }
        return ["$group-$key" => $value];
    }

    public function evaluateReturnType(string $inputType, int $index = null): string
    {
        if ($inputType !== TransformationDataTypeService::DEFAULT_ARRAY) {
            throw new InvalidConfigurationException(
                "'Unsupported input type '$inputType' for ToClassificationStoreKeyValuePair operator. Expected: " .
                TransformationDataTypeService::DEFAULT_ARRAY
            );
        }
        return TransformationDataTypeService::DEFAULT_ARRAY;
    }

    public function generateResultPreview($inputData): string
    {
        return json_encode($inputData);
    }

    public function getGroupId(mixed $group, ?int $storeId)
    {
        if ($storeId !== null && !is_numeric($group)) {
            $group = $this->groupRepository->getByName($group, $storeId)?->getId();
        }
        return $group;
    }

    public function getKeyId(mixed $key, ?int $storeId)
    {
        if ($storeId !== null && !is_numeric($key)) {
            $key = $this->keyRepository->getByName($key, $storeId)?->getId();
        }
        return $key;
    }
}
