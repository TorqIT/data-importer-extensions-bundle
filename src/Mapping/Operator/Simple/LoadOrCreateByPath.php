<?php

declare(strict_types=1);

namespace TorqIT\DataImporterExtensionsBundle\Mapping\Operator\Simple;

use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Bundle\DataImporterBundle\Mapping\Operator\AbstractOperator;
use Pimcore\Bundle\DataImporterBundle\Mapping\Type\TransformationDataTypeService;
use Pimcore\Bundle\DataImporterBundle\PimcoreDataImporterBundle;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Contracts\Service\Attribute\Required;
use Throwable;
use TorqIT\DataImporterExtensionsBundle\Helper\PathNodeOptions;
use TorqIT\DataImporterExtensionsBundle\Service\PathNodeResolver;

/**
 * Turns a delimited taxonomy string ("Venting > SnapRabbit ... > End Pipe") into a
 * data object beneath a fixed path prefix, creating any missing node on the way down.
 *
 * Returns the leaf object (or the whole branch, with returnAllLevels), so the result
 * maps straight onto a relation field. Set the import's overwrite mode to "merge" to
 * add to existing relations rather than replace them.
 *
 * Splits on the separator FIRST and sanitises each segment afterwards: term names in
 * the wild contain forward slashes ("... Single Wall Rigid / Flexible Venting"), so a
 * blind str_replace(' > ', '/') would invent tree levels.
 */
#[AutoconfigureTag(name: 'pimcore.datahub.data_importer.operator', attributes: ['type' => 'loadOrCreateByPath'])]
class LoadOrCreateByPath extends AbstractOperator
{
    protected string $pathPrefix = '';

    protected string $separator = ' > ';

    protected string $objectClass = '';

    protected string $nameField = '';

    protected bool $createIfNotFound = true;

    protected bool $publishOnCreate = true;

    protected bool $exactMatch = false;

    protected bool $logNearMisses = true;

    protected bool $returnAllLevels = false;

    protected PathNodeResolver $pathNodeResolver;

    #[Required]
    public function setPathNodeResolver(PathNodeResolver $pathNodeResolver): void
    {
        $this->pathNodeResolver = $pathNodeResolver;
    }

    public function setSettings(array $settings): void
    {
        $this->pathPrefix = trim((string) ($settings['pathPrefix'] ?? ''));
        $this->separator = (string) ($settings['separator'] ?? ' > ');
        $this->objectClass = trim((string) ($settings['objectClass'] ?? ''));
        $this->nameField = trim((string) ($settings['nameField'] ?? ''));
        $this->createIfNotFound = (bool) ($settings['createIfNotFound'] ?? true);
        $this->publishOnCreate = (bool) ($settings['publishOnCreate'] ?? true);
        $this->exactMatch = (bool) ($settings['exactMatch'] ?? false);
        $this->logNearMisses = (bool) ($settings['logNearMisses'] ?? true);
        $this->returnAllLevels = (bool) ($settings['returnAllLevels'] ?? false);

        if ($this->separator === '') {
            throw new InvalidConfigurationException('Separator must not be empty.');
        }
    }

    /**
     * @return DataObject|array<DataObject|string>|string|null
     */
    public function process($inputData, bool $dryRun = false)
    {
        $returnScalar = !is_array($inputData) && !$this->returnAllLevels;
        $inputs = is_array($inputData) ? $inputData : [$inputData];

        $root = $this->resolveRoot();
        $options = new PathNodeOptions(
            class: $this->resolveClass(),
            nameField: $this->nameField,
            publishOnCreate: $this->publishOnCreate,
            exactMatch: $this->exactMatch,
        );

        $results = [];
        $previousHideUnpublished = DataObject::getHideUnpublished();
        DataObject::setHideUnpublished(false);

        try {
            foreach ($inputs as $raw) {
                if (!is_string($raw) || trim($raw) === '') {
                    continue;
                }

                $segments = $this->splitPath($raw);
                if ($segments === []) {
                    continue;
                }

                $branch = $this->walk($root, $segments, $raw, $options, $dryRun);
                if ($branch === []) {
                    continue;
                }

                if ($this->returnAllLevels) {
                    foreach ($branch as $node) {
                        $results[] = $node;
                    }
                } else {
                    $results[] = end($branch);
                }
            }
        } finally {
            DataObject::setHideUnpublished($previousHideUnpublished);
        }

        $results = array_values(array_filter($results, static fn ($result) => $result !== null));

        if ($returnScalar) {
            return $results[0] ?? null;
        }

        return $results;
    }

    /**
     * @param string[] $segments
     *
     * @return array<DataObject|string>
     */
    private function walk(DataObject $root, array $segments, string $raw, PathNodeOptions $options, bool $dryRun): array
    {
        $branch = [];
        $parent = $root;

        foreach ($segments as $index => $segment) {
            $found = $this->pathNodeResolver->findChild($parent, $segment, $options);

            if ($found !== null) {
                if ($this->logNearMisses && $found->getKey() !== PathNodeResolver::toKey($segment)) {
                    $this->log('info', sprintf(
                        'Matched `%s` to `%s` only after normalising punctuation (row value: %s)',
                        $segment,
                        $found->getRealFullPath(),
                        $raw
                    ));
                }

                $branch[] = $found;
                $parent = $found;

                continue;
            }

            $plannedPath = rtrim($parent->getRealFullPath(), '/') . '/' . PathNodeResolver::toKey($segment);

            if (!$this->createIfNotFound) {
                $this->log('warning', sprintf('No object at `%s` and creation is disabled (row value: %s)', $plannedPath, $raw));

                return [];
            }

            if ($dryRun) {
                // Preview only. Nothing is written, so show the branch the run would build.
                $preview = rtrim($parent->getRealFullPath(), '/');
                foreach (array_slice($segments, $index) as $pending) {
                    $preview .= '/' . PathNodeResolver::toKey($pending);
                }

                return ['[would create] ' . $preview];
            }

            try {
                $created = $this->pathNodeResolver->createChild($parent, $segment, $options);
            } catch (Throwable $e) {
                $this->log('error', sprintf('Failed to create `%s`: %s (row value: %s)', $plannedPath, $e->getMessage(), $raw));

                return [];
            }

            $this->log('info', sprintf('Created `%s`', $created->getRealFullPath()));

            $branch[] = $created;
            $parent = $created;
        }

        return $branch;
    }

    /**
     * @return string[]
     */
    private function splitPath(string $raw): array
    {
        $segments = [];

        foreach (explode($this->separator, $raw) as $part) {
            // Collapse whitespace runs: source sheets carry stray double spaces
            // inside term names ("Cleat Fold Duct -  26 Gauge").
            $part = trim((string) preg_replace('/[\s\x{00A0}]+/u', ' ', $part));
            if ($part !== '') {
                $segments[] = $part;
            }
        }

        return $segments;
    }

    private function resolveRoot(): DataObject
    {
        if ($this->pathPrefix === '') {
            throw new InvalidConfigurationException('Path prefix is required.');
        }

        $path = rtrim($this->pathPrefix, '/');
        $root = DataObject::getByPath($path === '' ? '/' : $path);

        // Never created automatically: a typo in the prefix would otherwise silently
        // build a second parallel tree alongside the real one.
        if (!$root instanceof DataObject) {
            throw new InvalidConfigurationException(sprintf(
                'Path prefix `%s` does not exist. Check the spelling — it is not created for you.',
                $this->pathPrefix
            ));
        }

        return $root;
    }

    private function resolveClass(): ClassDefinition
    {
        if ($this->objectClass === '') {
            throw new InvalidConfigurationException('Object class is required.');
        }

        $class = ClassDefinition::getByName($this->objectClass) ?? ClassDefinition::getById($this->objectClass);

        if (!$class instanceof ClassDefinition) {
            throw new InvalidConfigurationException(sprintf('Class `%s` not found.', $this->objectClass));
        }

        return $class;
    }

    private function log(string $level, string $message): void
    {
        $this->applicationLogger->$level($message, [
            'component' => PimcoreDataImporterBundle::LOGGER_COMPONENT_PREFIX . $this->configName,
        ]);
    }

    public function evaluateReturnType(string $inputType, ?int $index = null): string
    {
        if ($this->returnAllLevels) {
            return TransformationDataTypeService::DATA_OBJECT_ARRAY;
        }

        return match ($inputType) {
            TransformationDataTypeService::DEFAULT_TYPE => TransformationDataTypeService::DATA_OBJECT,
            TransformationDataTypeService::DEFAULT_ARRAY => TransformationDataTypeService::DATA_OBJECT_ARRAY,
            default => throw new InvalidConfigurationException(sprintf(
                "Unsupported input type '%s' for load or create by path operator at transformation position %s",
                $inputType,
                $index
            )),
        };
    }

    public function generateResultPreview($inputData)
    {
        $returnScalar = !is_array($inputData);
        $items = $returnScalar ? [$inputData] : $inputData;

        foreach ($items as &$item) {
            if ($item instanceof DataObject) {
                $item = 'DataObject: ' . $item->getRealFullPath() . ' (ID: ' . $item->getId() . ')';
            }
        }
        unset($item);

        return $returnScalar ? reset($items) : $items;
    }
}
