<?php declare(strict_types=1);

namespace App\DataImporter\Resolver\Load;

use Pimcore\Bundle\DataImporterBundle\Resolver\Load\AbstractLoad;
use Pimcore\Model\DataObject;
use Pimcore\Model\Element\ElementInterface;

/**
 * Loads a data object by its Pimcore system key (o_key / object filename in tree).
 *
 * The built-in AttributeStrategy cannot handle system fields because it queries
 * the class-specific table (object_CLASSID) which does not contain the key column.
 * This strategy queries via the Listing class, which uses the correct system table.
 */
class LoadByKeyStrategy extends AbstractLoad
{
    protected bool $includeUnpublished = false;
    protected ?string $searchPath = null;

    public function setSettings(array $settings): void
    {
        parent::setSettings($settings);

        $this->includeUnpublished = $settings['includeUnpublished'] ?? false;
        $this->searchPath = !empty($settings['searchPath']) ? $settings['searchPath'] : null;
    }

    public function loadElementByIdentifier($identifier): ?ElementInterface
    {
        $sql = sprintf(
            'SELECT `id` FROM `objects` WHERE `key` = %s AND `classId` = %s',
            $this->db->quote($identifier),
            $this->db->quote($this->dataObjectClassId)
        );

        if ($this->searchPath !== null) {
            $normalizedPath = rtrim($this->searchPath, '/') . '/';
            $sql .= sprintf(' AND `path` LIKE %s', $this->db->quote($normalizedPath . '%'));
        }

        $sql .= ' LIMIT 1';

        $id = $this->db->fetchOne($sql);

        if (!$id) {
            return null;
        }

        if ($this->includeUnpublished) {
            DataObject::setHideUnpublished(false);
        }

        try {
            return DataObject::getById((int) $id);
        } finally {
            if ($this->includeUnpublished) {
                DataObject::setHideUnpublished(true);
            }
        }
    }

    public function loadFullIdentifierList(): array
    {
        $sql = sprintf('SELECT `key` FROM `objects` WHERE `classId` = %s', $this->db->quote($this->dataObjectClassId));

        if ($this->searchPath !== null) {
            $normalizedPath = rtrim($this->searchPath, '/') . '/';
            $sql .= sprintf(' AND `path` LIKE %s', $this->db->quote($normalizedPath . '%'));
        }

        return $this->db->fetchFirstColumn($sql);
    }
}