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
final class TestController
{

    use ControllerTrait;

    /**
     * @return Response
     */
    #[Route('/test/route', methods: ['GET'])]
    public function getNodesAction(): Response
    {
        return $this->getResponse([]);
    }

}
