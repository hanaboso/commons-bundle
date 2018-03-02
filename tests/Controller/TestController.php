<?php declare(strict_types=1);

namespace Tests\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TestController
 *
 * @package Tests\Controller
 */
class TestController extends FOSRestController
{

    /**
     * @Route("/test/route")
     * @Method({"GET", "OPTIONS"})
     *
     * @return Response
     */
    public function getNodesAction(): Response
    {
        return new Response();
    }

}