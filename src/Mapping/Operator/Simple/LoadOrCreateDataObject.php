<?php

declare(strict_types=1);

namespace TorqIT\DataImporterExtensionsBundle\Mapping\Operator\Simple;

use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Bundle\DataImporterBundle\Mapping\Operator\Simple\LoadDataObject;
use Pimcore\Bundle\DataImporterBundle\PimcoreDataImporterBundle;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Service;

class LoadOrCreateDataObject extends LoadDataObject
{
    protected bool $createIfNotFound = false;

    protected string $createPath = '/';

    public function setSettings(array $settings): void
    {
        parent::setSettings($settings);
        $this->createIfNotFound = (bool) ($settings['createIfNotFound'] ?? false);
        $this->loadUnpublished = $this->loadUnpublished || $this->createIfNotFound;
        $this->createPath = $settings['createPath'] ?? '/';
    }

    public function process($inputData, bool $dryRun = false)
    {
        $result = parent::process($inputData, $dryRun);

        if (!$this->createIfNotFound) {
            return $result;
        }

        $returnScalar = !is_array($inputData);

        if ($returnScalar && $result !== null) {
            return $result;
        }
        if (!$returnScalar && !empty($result)) {
            return $result;
        }

        if ($dryRun) {
            return $returnScalar ? null : [];
        }

        // Not found — create
        $inputItems = $returnScalar ? [$inputData] : $inputData;
        $objects = [];
        foreach ($inputItems as $data) {
            if (empty($data) && $data !== '0') {
                continue;
            }

            try {
                $created = $this->createDataObject(trim((string) $data));
                $objects[] = $created;
                $this->applicationLogger->info(
                    sprintf('Created new data object with key `%s` at path `%s`', $created->getKey(), $this->createPath),
                    ['component' => PimcoreDataImporterBundle::LOGGER_COMPONENT_PREFIX . $this->configName]
                );
            } catch (\Throwable $e) {
                $this->applicationLogger->error(
                    sprintf('Failed to create data object from `%s`: %s', $data, $e->getMessage()),
                    ['component' => PimcoreDataImporterBundle::LOGGER_COMPONENT_PREFIX . $this->configName]
                );
            }
        }

        if ($returnScalar) {
            return !empty($objects) ? reset($objects) : null;
        }

        return $objects;
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

        $className = '\\Pimcore\\Model\\DataObject\\' . ucfirst($class->getName());
        $parentFolder = Service::createFolderByPath($this->createPath);

        $fullPath = rtrim($this->createPath, '/') . '/' . $keyValue;
        $existing = DataObject::getByPath($fullPath);
        if ($existing instanceof DataObject\Concrete) {
            return $existing;
        }

        $object = new $className();
        $object->setParent($parentFolder);
        $object->setKey($keyValue);
        $object->setPublished(false);

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

        $object->save();

        return $object;
    }
}
