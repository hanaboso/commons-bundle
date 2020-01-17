<?php declare(strict_types=1);

namespace CommonsBundleTests\Controller;

use Hanaboso\Utils\Traits\ControllerTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TestController
 *
 * @package CommonsBundleTests\Controller
 */
class TestController
{

    use ControllerTrait;

    /**
     * @Route("/test/route", methods={"GET", "OPTIONS"})
     *
     * @return Response
     */
    public function getNodesAction(): Response
    {
        return $this->getResponse([]);
    }

}
