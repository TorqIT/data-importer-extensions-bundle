<?php

declare(strict_types=1);

namespace TorqIT\DataImporterExtensionsBundle\Controller;

use Pimcore\Bundle\AdminBundle\Controller\AdminAbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use TorqIT\DataImporterExtensionsBundle\Exception\DoctrineConnectionsNotReturnedAsArrayException;

/**
 * @Route("/admin/pimcoredataimporter/")
 */
class ConnectionController extends AdminAbstractController
{
    /**
     * @Route("get-connections", name="pimcore_dataimporter_connections", methods={"GET"})
     *
     * @throws DoctrineConnectionsNotReturnedAsArrayException
     */
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
