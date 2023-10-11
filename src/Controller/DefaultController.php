<?php

namespace TorqIT\DataImporterExtensionsBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/torqitpimcoredataimporter/")
 */
class DefaultController extends FrontendController
{
    /**
     * @Route("/torq_it_data_importer_extensions")
     */
    public function indexAction(Request $request)
    {
        return new Response('Hello world from torq_it_data_importer_extensions');
    }

    /**
     * @Route("getSqlConnections")
     */
    public function getSqlConnectionsAction(Request $request, ManagerRegistry $managerRegistry)
    {
        $connections = $managerRegistry->getConnectionNames();

        $mapped = [];

        foreach($connections as $key => $value){
            $mapped[] = [
                'name' => $key,
                'value' => $value
            ];
        }
        return new Response(json_encode($mapped));
    }
    
}
