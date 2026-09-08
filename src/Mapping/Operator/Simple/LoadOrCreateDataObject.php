<?php

declare(strict_types=1);

namespace TorqIT\DataImporterExtensionsBundle\Mapping\Operator\Simple;

use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Bundle\DataImporterBundle\PimcoreDataImporterBundle;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Service;
use Throwable;
use TorqIT\DataImporterExtensionsBundle\Override\CustomLoadDataObject;

class LoadOrCreateDataObject extends CustomLoadDataObject
{
    protected bool $createIfNotFound = false;

    protected bool $publishOnCreate = false;

    protected string $createPath = '/';

    public function setSettings(array $settings): void
    {
        parent::setSettings($settings);
        $this->createIfNotFound = (bool) ($settings['createIfNotFound'] ?? false);
        $this->publishOnCreate = (bool) ($settings['publishOnCreate'] ?? false);
        $this->loadUnpublished = $this->loadUnpublished || $this->createIfNotFound;
        $this->createPath = $settings['createPath'] ?? '/';
    }

    public function process($inputData, bool $dryRun = false)
    {
        if (!$this->createIfNotFound || $dryRun) {
            return parent::process($inputData, $dryRun);
        }

        if (!is_array($inputData)) {
            return $this->loadOrCreate($inputData);
        }

        // Resolve every value on its own. The parent returns only the objects it found, so a
        // whole-array check cannot tell which values are missing: as soon as one value in the
        // cell resolved, the rest were silently dropped instead of created.
        $objects = [];
        foreach ($inputData as $data) {
            $object = $this->loadOrCreate($data);
            if ($object instanceof DataObject) {
                $objects[] = $object;
            }
        }

        return $objects;
    }

    private function loadOrCreate(mixed $data): ?DataObject
    {
        $object = parent::process($data);
        if ($object instanceof DataObject) {
            return $object;
        }

        if (empty($data) && $data !== '0') {
            return null;
        }

        try {
            $created = $this->createDataObject(trim((string) $data));
            $this->applicationLogger->info(
                sprintf('Created new data object with key `%s` at `%s`', $created->getKey(), $created->getRealFullPath()),
                ['component' => PimcoreDataImporterBundle::LOGGER_COMPONENT_PREFIX . $this->configName]
            );

            return $created;
        } catch (Throwable $e) {
            $this->applicationLogger->error(
                sprintf('Failed to create data object from `%s`: %s', $data, $e->getMessage()),
                ['component' => PimcoreDataImporterBundle::LOGGER_COMPONENT_PREFIX . $this->configName]
            );

            return null;
        }
    }

    private function createDataObject(string $keyValue): DataObject\Concrete
    {
        if ($this->loadStrategy !== self::LOAD_STRATEGY_ATTRIBUTE || empty($this->attributeDataObjectClassId)) {
            throw new InvalidConfigurationException(
                'Create if not found requires the "attribute" load strategy with a class selected.'
            );
        }

        $class = ClassDefinition::getById($this->attributeDataObjectClassId);
        if (empty($class)) {
            throw new InvalidConfigurationException(
                sprintf('Class `%s` not found.', $this->attributeDataObjectClassId)
            );
        }

        $safeKey = Service::getValidKey($keyValue, 'object');
        $className = '\\Pimcore\\Model\\DataObject\\' . ucfirst($class->getName());
        $parentFolder = Service::createFolderByPath($this->createPath);

        $fullPath = rtrim($this->createPath, '/') . '/' . $safeKey;
        $existing = DataObject::getByPath($fullPath);
        if ($existing instanceof DataObject\Concrete) {
            return $existing;
        }

        $object = new $className();
        $object->setParent($parentFolder);
        $object->setKey($safeKey);
        $object->setPublished($this->publishOnCreate);

        if ($this->attributeName) {
            $setter = 'set' . ucfirst($this->attributeName);
            if (method_exists($object, $setter)) {
                if ($this->attributeLanguage) {
                    $object->$setter($keyValue, $this->attributeLanguage);
                } else {
                    $object->$setter($keyValue);
                }
            }
        }

        try {
            $object->save();
        } catch (Throwable $e) {
            // Under parallel processing two workers can create the same missing object at
            // once; the loser hits the unique path index. Re-fetch the winner's object so
            // this value still resolves instead of being dropped.
            $existing = DataObject::getByPath($fullPath, ['force' => true]);
            if ($existing instanceof DataObject\Concrete) {
                return $existing;
            }

            throw $e;
        }

        return $object;
    }
}
