<?php

declare(strict_types=1);

namespace TorqIT\DataImporterExtensionsBundle\DataSource\Loader;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Pimcore;
use Pimcore\Bundle\DataImporterBundle\DataSource\Loader\DataLoaderInterface;
use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Logger;
use Symfony\Component;
use TorqIT\DataImporterExtensionsBundle\Exception\FetchDatabaseDataException;
use TorqIT\DataImporterExtensionsBundle\Exception\InvalidConnectionException;

class SqlDataLoader implements DataLoaderInterface
{
    private string $connectionName;
    private string $sql;
    private string $importFilePath;
    private Connection $connection;

    public function __construct(protected Component\Filesystem\Filesystem $filesystem)
    {

    }

    /**
     * @throws InvalidConfigurationException
     * @throws InvalidConnectionException
     * @throws FetchDatabaseDataException
     */
    public function loadData(): string
    {
        $this->setUpConnection();
        $this->setUpImportFilePath();

        try {
            $result = $this->connection->fetchAllAssociative($this->sql);
        } catch (Exception $e) {
            Logger::error($e->getMessage());

            throw new FetchDatabaseDataException(
                sprintf('Cannot fetch database data due to error: %s', $e->getMessage())
            );
        }

        $filesystemLocal = new Filesystem(new LocalFilesystemAdapter('/'));

        try {
            $stream = fopen('php://temp', 'r+');
            fwrite($stream, json_encode($result));
            rewind($stream);
            $filesystemLocal->writeStream($this->importFilePath, $stream);

            return $this->importFilePath;
        } catch (FilesystemException $e) {
            Logger::error($e->getMessage());

            throw new InvalidConfigurationException(
                sprintf(
                    'Could not create JSON file based on database data to local tmp file `%s`',
                    $this->importFilePath
                )
            );
        }
    }

    public function cleanup(): void
    {
        $this->connection->close();

        unlink($this->importFilePath);
    }

    /**
     * @throws InvalidConfigurationException
     */
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

    /**
     * @throws InvalidConnectionException
     */
    private function setUpConnection(): void
    {
        $connection = Pimcore::getContainer()->get($this->connectionName);

        if (!$connection instanceof Connection) {
            throw new InvalidConnectionException('Connection not found or connection not exist');
        }

        $this->connection = $connection;
    }

    private function setUpImportFilePath(): void
    {
        $folder = PIMCORE_PRIVATE_VAR . '/tmp/datahub/dataimporter/sql-loader/';
        $this->filesystem->mkdir($folder, 0775);

        $this->importFilePath = $folder . uniqid('sql-import-');
    }
}
