<?php

declare(strict_types=1);

namespace TorqIT\DataImporterExtensionsBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use TorqIT\DataImporterExtensionsBundle\Exception\DoctrineConnectionsNotReturnedAsArrayException;

// FIXME: Change to studio path and extend correct controller
#[Route("/admin/pimcoredataimporter")]
class ConnectionController
{
    /** @throws DoctrineConnectionsNotReturnedAsArrayException */
    #[Route("/get-bulk-connections", name: 'pimcore_dataimporter_bulk_connections', methods: ['GET'])]
    public function getConnectionsAction(): JsonResponse
    {
        $connections = $this->getParameter('doctrine.connections');

        if (!is_array($connections)) {
            throw new DoctrineConnectionsNotReturnedAsArrayException('Doctrine connection not returned as array');
        }

        $mappedConnections = array_map(fn ($key, $value): array => [
            'name' => $key,
            'value' => $value
        ], array_keys($connections), $connections);

        return $this->json($mappedConnections);
    }
}
