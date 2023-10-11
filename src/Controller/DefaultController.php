<?php

namespace TorqIT\DataImporterExtensionsBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Pimcore\Bundle\AdminBundle\Controller\AdminAbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/torqitpimcoredataimporter/")
 */
class DefaultController extends AdminAbstractController
{
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
