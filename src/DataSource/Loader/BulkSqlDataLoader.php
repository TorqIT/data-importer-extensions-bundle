<?php

declare(strict_types=1);

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace TorqIT\DataImporterExtensionsBundle\DataSource\Loader;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Pimcore;
use Pimcore\Bundle\DataImporterBundle\DataSource\Loader\DataLoaderInterface;
use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Symfony\Component;

class BulkSqlLoader implements DataLoaderInterface
{
    private string $connection;
    private string $select;
    private string $from;
    private string $where;
    private string $groupBy;

    private string $importFilePath;
    private Connection $databaseConnection;

    public function __construct(private Component\Filesystem\Filesystem $filesystem)
    {
    }

    /**
     * @throws InvalidConfigurationException
     * @throws Exception
     * @throws FilesystemException
     */
    public function loadData(): string
    {
        $this->setUpConnection();
        $this->setUpImportFilePath();

        $queryBuilder = $this->databaseConnection->createQueryBuilder();
        $queryBuilder->select($this->select)
            ->from($this->from);

        if (!empty($this->where)) {
            $queryBuilder->where($this->where);
        }

        if (!empty($this->groupBy)) {
            $queryBuilder->groupBy($this->groupBy);
        }

        $result = $queryBuilder->execute()->fetchAllAssociative();

        $filesystemLocal = new Filesystem(new LocalFilesystemAdapter('/'));
        $stream = fopen('php://temp', 'r+');
        $resultAsJson = json_encode($result);

        fwrite($stream, $resultAsJson);
        rewind($stream);

        $filesystemLocal->writeStream($this->importFilePath, $stream);

        return $this->importFilePath;
    }

    public function cleanup(): void
    {
        $this->databaseConnection->close();

        unlink($this->importFilePath);
    }

    /**
     * @throws InvalidConfigurationException
     */
    public function setSettings(array $settings): void
    {
        if (empty($settings['connection'])) {
            throw new InvalidConfigurationException('Empty connection.');
        }
        $this->connection = $settings['connection'];

        if (empty($settings['select'])) {
            throw new InvalidConfigurationException('Empty select.');
        }
        $this->select = $settings['select'];

        if (empty($settings['from'])) {
            throw new InvalidConfigurationException('Empty from.');
        }
        $this->from = $settings['from'];

        $this->where = $settings['where'];
        $this->groupBy = $settings['groupBy'];
    }

    /**
     * @throws InvalidConfigurationException
     */
    private function setUpConnection(): void
    {
        $container = Pimcore::getContainer();
        $databaseConnection = null;

        if ($container instanceof Component\DependencyInjection\ContainerInterface) {
            $databaseConnection = $container->get($this->connection);
        }

        if (!$databaseConnection instanceof Connection) {
            throw new InvalidConfigurationException('Connection not found.');
        }

        $this->databaseConnection = $databaseConnection;
    }

    private function setUpImportFilePath(): void
    {
        $folder = PIMCORE_PRIVATE_VAR . '/tmp/datahub/dataimporter/bulk-sql-loader/';
        $this->filesystem->mkdir($folder, 0775);

        $this->importFilePath = $folder . uniqid('bulk-sql-import-');
    }
}