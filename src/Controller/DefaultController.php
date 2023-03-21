<?php

namespace TorqIT\DataImporterExtensionsBundle\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends FrontendController
{
    /**
     * @Route("/torq_it_data_importer_extensions")
     */
    public function indexAction(Request $request)
    {
        return new Response('Hello world from torq_it_data_importer_extensions');
    }
}
