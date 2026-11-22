<?php

declare(strict_types=1);

namespace TorqIT\DataImporterExtensionsBundle\Service;

use Exception;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Element\Service as ElementService;
use Symfony\Component\Lock\LockFactory;
use TorqIT\DataImporterExtensionsBundle\Helper\PathNodeOptions;

/**
 * Finds, and optionally creates, a single level of a data object tree.
 *
 * Matching is punctuation-blind by default: source sheets spell the same term as
 * `Elbows - 45° - 4" - 8"` and `Elbows — 45° — 4″ - 8″`, and matching those
 * literally builds duplicate branches.
 */
class PathNodeResolver
{
    private const string LOCK_PREFIX = 'data-importer-extensions-path-node-';

    /** @var array<string, int|null> cacheKey => object id (null = known absent) */
    private array $childCache = [];

    public function __construct(private readonly LockFactory $lockFactory)
    {
    }

    public function findChild(DataObject $parent, string $segment, PathNodeOptions $options): ?DataObject
    {
        $needle = $options->exactMatch ? self::toKey($segment) : self::normalize($segment);
        $cacheKey = $parent->getId() . "\0" . ($options->exactMatch ? 'e' : 'n') . "\0" . $needle;

        if (array_key_exists($cacheKey, $this->childCache)) {
            $id = $this->childCache[$cacheKey];

            return $id === null ? null : DataObject::getById($id);
        }

        $match = null;

        foreach ($this->listChildren($parent) as $child) {
            if ($options->exactMatch) {
                if ($child->getKey() === $needle) {
                    $match = $child;

                    break;
                }

                continue;
            }

            if (self::normalize($child->getKey()) === $needle) {
                $match = $child;

                break;
            }

            // Fall back to the display name, for nodes whose key was degraded on creation.
            if ($options->nameField !== '' && $child instanceof Concrete) {
                $getter = 'get' . ucfirst($options->nameField);
                if (method_exists($child, $getter)) {
                    $name = $child->$getter();
                    if (is_string($name) && $name !== '' && self::normalize($name) === $needle) {
                        $match = $child;

                        break;
                    }
                }
            }
        }

        $this->childCache[$cacheKey] = $match?->getId();

        return $match;
    }

    public function createChild(DataObject $parent, string $segment, PathNodeOptions $options): DataObject
    {
        $key = self::toKey($segment);

        if ($key === '') {
            throw new Exception(sprintf('`%s` does not reduce to a usable object key.', $segment));
        }

        $fullPath = rtrim($parent->getRealFullPath(), '/') . '/' . $key;
        $lock = $this->lockFactory->createLock(self::LOCK_PREFIX . md5($fullPath), 30.0);
        $lock->acquire(true);

        try {
            // Re-check under the lock: parallel import workers race on shared parents.
            $existing = DataObject::getByPath($fullPath);
            if ($existing instanceof DataObject) {
                $this->remember($parent, $segment, $options, $existing);

                return $existing;
            }

            $className = '\\Pimcore\\Model\\DataObject\\' . ucfirst($options->class->getName());

            /** @var Concrete $node */
            $node = new $className();
            $node->setParent($parent);
            $node->setKey($key);
            $node->setPublished($options->publishOnCreate);

            if ($options->nameField !== '') {
                $setter = 'set' . ucfirst($options->nameField);
                if (method_exists($node, $setter)) {
                    // The untouched segment, not the sanitised key.
                    $node->$setter($segment);
                }
            }

            // Intermediate nodes are created from a path alone, so they cannot
            // satisfy mandatory fields on the class.
            $node->setOmitMandatoryCheck(true);
            $node->save();

            $this->remember($parent, $segment, $options, $node);

            return $node;
        } finally {
            $lock->release();
        }
    }

    private function remember(DataObject $parent, string $segment, PathNodeOptions $options, DataObject $node): void
    {
        $needle = $options->exactMatch ? self::toKey($segment) : self::normalize($segment);
        $this->childCache[$parent->getId() . "\0" . ($options->exactMatch ? 'e' : 'n') . "\0" . $needle] = $node->getId();
    }

    /**
     * @return iterable<DataObject>
     */
    private function listChildren(DataObject $parent): iterable
    {
        return $parent->getChildren(
            [DataObject::OBJECT_TYPE_OBJECT, DataObject::OBJECT_TYPE_FOLDER],
            true
        );
    }

    public static function toKey(string $segment): string
    {
        return ElementService::getValidKey(trim($segment), 'object');
    }

    /**
     * Folds the punctuation that distinguishes otherwise identical term names:
     * dash variants, curly quotes and primes, slashes (which getValidKey turns
     * into dashes anyway), repeated whitespace and case.
     */
    public static function normalize(string $value): string
    {
        // Primes and curly quotes first: NFKC decomposes U+2033 into two U+2032,
        // which would otherwise stop 4″ matching 4".
        $value = str_replace(
            ["\u{2018}", "\u{2019}", "\u{201A}", "\u{201B}", "\u{2032}", "\u{00B4}", '`',
             "\u{201C}", "\u{201D}", "\u{201E}", "\u{2033}", "\u{00AB}", "\u{00BB}"],
            ["'", "'", "'", "'", "'", "'", "'", '"', '"', '"', '"', '"', '"'],
            $value
        );

        if (class_exists(\Normalizer::class)) {
            $value = \Normalizer::normalize($value, \Normalizer::FORM_KC) ?: $value;
        }

        $value = str_replace("''", '"', $value);

        // Hyphen, non-breaking hyphen, figure/en/em/horizontal dash, minus, hyphen bullet.
        $value = (string) preg_replace('/[\x{2010}-\x{2015}\x{2212}\x{2043}]/u', '-', $value);
        $value = (string) preg_replace('/[\s\x{00A0}]+/u', ' ', $value);
        $value = (string) preg_replace('/\s*[-\/]\s*/u', '-', $value);

        return mb_strtolower(trim($value));
    }
}
