<?php declare(strict_types=1);

namespace Tests;

use Doctrine\ODM\MongoDB\DocumentManager;
use stdClass;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class ControllerTestCaseAbstract
 *
 * @package Tests
 */
abstract class ControllerTestCaseAbstract extends WebTestCase
{

    /**
     * @var KernelBrowser
     */
    protected $client;

    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var Session
     */
    protected $session;

    /**
     * ControllerTestCaseAbstract constructor.
     *
     * @param null   $name
     * @param array  $data
     * @param string $dataName
     */
    public function __construct($name = NULL, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        $this->dm     = self::$container->get('doctrine_mongodb.odm.default_document_manager');
        $this->client = self::createClient([], []);
        $this->dm->getConnection()->dropDatabase('pipes');
    }

    /**
     * @param mixed $document
     */
    protected function persistAndFlush($document): void
    {
        $this->dm->persist($document);
        $this->dm->flush($document);
    }

    /**
     * @param string $url
     *
     * @return stdClass
     */
    protected function sendGet(string $url): stdClass
    {
        $this->client->request('GET', $url);
        $response = $this->client->getResponse();

        return $this->formatResponse($response);
    }

    /**
     * @param string     $url
     * @param array      $parameters
     * @param array|null $content
     *
     * @return stdClass
     */
    protected function sendPost(string $url, array $parameters, ?array $content = NULL): stdClass
    {
        $this->client->request('POST', $url, $parameters, [], [], $content ? (string) json_encode($content) : '');
        $response = $this->client->getResponse();

        return $this->formatResponse($response);
    }

    /**
     * @param string     $url
     * @param array      $parameters
     * @param array|null $content
     *
     * @return stdClass
     */
    protected function sendPut(string $url, array $parameters, ?array $content = NULL): stdClass
    {
        $this->client->request('PUT', $url, $parameters, [], [], $content ? (string) json_encode($content) : '');
        $response = $this->client->getResponse();

        return $this->formatResponse($response);
    }

    /**
     * @param string $url
     *
     * @return stdClass
     */
    protected function sendDelete(string $url): stdClass
    {
        $this->client->request('DELETE', $url);
        $response = $this->client->getResponse();

        return $this->formatResponse($response);
    }

    /**
     * @param Response $response
     *
     * @return stdClass
     */
    protected function formatResponse(Response $response): stdClass
    {
        return (object) [
            'status'  => $response->getStatusCode(),
            'content' => json_decode((string) $response->getContent()),
        ];
    }

}
