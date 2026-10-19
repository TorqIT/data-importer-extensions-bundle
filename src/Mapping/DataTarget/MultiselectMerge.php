<?php

declare(strict_types=1);

namespace TorqIT\DataImporterExtensionsBundle\Mapping\DataTarget;

use Pimcore\Bundle\DataImporterBundle\Mapping\DataTarget\Direct;
use Pimcore\Model\DataObject;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Merges incoming values into a multiselect field instead of replacing it.
 *
 * Incoming data is an array of values (a scalar is treated as a single value, never split).
 * Existing values are kept, new values are appended in input order and duplicates are dropped
 * (exact match after trim). An empty source never clears the field - a sheet that lists only
 * the values to add must not remove the rest.
 * Works for plain, localized and objectbrick multiselects (same fieldName forms as "direct").
 */
#[AutoconfigureTag(name: 'pimcore.datahub.data_importer.data_target', attributes: ['type' => 'multiselectMerge'])]
class MultiselectMerge extends Direct
{
    public function setSettings(array $settings): void
    {
        // Merging only makes sense on top of the existing value, so the direct write switches
        // are pinned: always write into a non-empty target, never write an empty source.
        $settings['writeIfTargetIsNotEmpty'] = true;
        $settings['writeIfSourceIsEmpty'] = false;

        parent::setSettings($settings);
    }

    protected function doAssignData($valueContainer, $fieldName, $data): void
    {
        $incoming = self::normalize($data);
        if ($incoming === []) {
            return;
        }

        $hideUnpublished = DataObject::getHideUnpublished();
        DataObject::setHideUnpublished(false);
        $getter = 'get' . ucfirst($fieldName);
        $existing = self::normalize($valueContainer->$getter($this->language));
        DataObject::setHideUnpublished($hideUnpublished);

        $merged = self::merge($existing, $incoming);
        if ($merged === $existing) {
            return;
        }

        parent::doAssignData($valueContainer, $fieldName, $merged);
    }

    /**
     * @param list<string> $existing
     * @param list<string> $incoming
     *
     * @return list<string>
     */
    public static function merge(array $existing, array $incoming): array
    {
        return array_values(array_unique(array_merge($existing, $incoming)));
    }

    /**
     * Array (or single scalar) to a trimmed, non-empty list of strings.
     *
     * @return list<string>
     */
    public static function normalize(mixed $data): array
    {
        if ($data === null || $data === '' || $data === []) {
            return [];
        }

        if (!is_array($data)) {
            $data = [$data];
        }

        $values = [];
        foreach ($data as $value) {
            if (is_array($value) || is_object($value)) {
                continue;
            }
            $value = trim((string) $value);
            if ($value !== '') {
                $values[] = $value;
            }
        }

        return array_values(array_unique($values));
    }
}
