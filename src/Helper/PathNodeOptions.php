<?php

declare(strict_types=1);

namespace TorqIT\DataImporterExtensionsBundle\Helper;

use Pimcore\Model\DataObject\ClassDefinition;

/**
 * Plain value object, deliberately in Helper\ rather than Service\ so the
 * container does not try to autowire it.
 */
final readonly class PathNodeOptions
{
    public function __construct(
        public ClassDefinition $class,
        public string $nameField = '',
        public bool $publishOnCreate = true,
        public bool $exactMatch = false,
    ) {
    }
}
