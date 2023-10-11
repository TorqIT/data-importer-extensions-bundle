<?php

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

use Pimcore\Bundle\DataImporterBundle\DataSource\Loader\DataLoaderInterface;
use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Pimcore\Logger;
use Doctrine\DBAL\Connection;
use Symfony\Component;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;

class SqlDataLoader implements DataLoaderInterface
{
    
    /**
     * @var string
     */
    protected $importFilePath;

    /**
     * @var string
     */
    protected $connectionName;

    /**
     * @var string
     */
    protected $sql;

    /**
     * @var Connection
     */
    private Connection $connection;


    public function __construct(protected Component\Filesystem\Filesystem $filesystem) {

    }

    public function loadData(): string
    {
        $this->connection = \Pimcore::getContainer()->get($this->connectionName);

        $result = $this->connection->fetchAllAssociative($this->sql);


        $folder = PIMCORE_PRIVATE_VAR . '/tmp/datahub/dataimporter/sql-loader/';
        $this->filesystem->mkdir($folder, 0775);
        $this->importFilePath = $folder . uniqid('sql-import-');

        $filesystemLocal = new Filesystem(new LocalFilesystemAdapter('/'));

        try {


            $stream = fopen('php://temp', 'r+');
            fwrite($stream, json_encode($result));
            rewind($stream);

            $filesystemLocal->writeStream($this->importFilePath, $stream);

            return $this->importFilePath;
        } catch (FilesystemException $e) {
            Logger::error($e);
            throw new InvalidConfigurationException(sprintf('Could not copy from remote location `%s` to local tmp file `%s`', $loggingRemoteUrl, $this->importFilePath));
        }

    }

    public function cleanup(): void
    {
        $this->connection->close();
    }

    public function setSettings(array $settings): void
    {
        if (empty($settings['connectionName'])) {
            throw new InvalidConfigurationException('Empty Connection Name.');
        }
        $this->connectionName = $settings['connectionName'];

        if (empty($settings['sql'])) {
            throw new InvalidConfigurationException('Empty SQL');
        }
        $this->sql = $settings['sql'];
    }
}