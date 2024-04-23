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
use TorqIT\DataImporterExtensionsBundle\Exception\NotResourceException;
use TorqIT\DataImporterExtensionsBundle\Exception\ParseArrayToJsonException;

class SqlDataLoader implements DataLoaderInterface
{
    private string $connection;
    private string $select;
    private string $from;
    private string $where;
    private string $groupBy;
    private ?int $limit;

    private string $importFilePath;
    private Connection $databaseConnection;

    public function __construct(protected Component\Filesystem\Filesystem $filesystem)
    {
    }

    /**
     * @throws InvalidConfigurationException
     * @throws InvalidConnectionException
     * @throws FetchDatabaseDataException
     * @throws NotResourceException
     * @throws ParseArrayToJsonException
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

        if($this->limit){
            $queryBuilder->setMaxResults($this->limit);
        }

        $results = $queryBuilder->executeQuery();

        $filesystemLocal = new Filesystem(new LocalFilesystemAdapter('/'));
        $stream = fopen('php://temp', 'r+');
        $columnNamesAdded = false;
        while (($result = $results->fetchAssociative()) !== false) {
            if (!$columnNamesAdded) {
                fputcsv($stream, array_keys($result));
                $columnNamesAdded = true;
            }
            fputcsv($stream, $result);
        }

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
     * @param array<string, string> $settings
     *
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

        $this->limit = array_key_exists('limit', $settings) ? intval($settings['limit']) : null;
    }

    /**
     * @throws InvalidConnectionException
     */
    private function setUpConnection(): void
    {
        $container = Pimcore::getContainer();
        $databaseConnection = null;

        if ($container instanceof Component\DependencyInjection\ContainerInterface) {
            $databaseConnection = $container->get($this->connection);
        }

        if (!$databaseConnection instanceof Connection) {
            throw new InvalidConnectionException('Connection not found');
        }

        $this->databaseConnection = $databaseConnection;
    }

    private function setUpImportFilePath(): void
    {
        $folder = PIMCORE_PRIVATE_VAR . '/tmp/datahub/dataimporter/sql-loader/';
        $this->filesystem->mkdir($folder, 0775);

        $this->importFilePath = $folder . uniqid('sql-import-');
    }
}
