<?php declare(strict_types=1);

namespace Tests\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TestController
 *
 * @package Tests\Controller
 */
class TestController extends FOSRestController
{

    /**
     * @Route("/test/route", methods={"GET", "OPTIONS"})
     *
     * @return Response
     */
    public function getNodesAction(): Response
    {
        return new Response();
    }

}