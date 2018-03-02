<?php declare(strict_types=1);

namespace Tests;

use Doctrine\ODM\MongoDB\DocumentManager;
use Nette\Utils\Json;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class ControllerTestCaseAbstract
 *
 * @package Tests
 */
abstract class ControllerTestCaseAbstract extends WebTestCase
{

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var Session
     */
    protected $session;

    /**
     * DatabaseTestCase constructor.
     *
     * @param null   $name
     * @param array  $data
     * @param string $dataName
     */
    public function __construct($name = NULL, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        self::bootKernel();
        $this->container = self::$kernel->getContainer();
        $this->dm        = $this->container->get('doctrine_mongodb.odm.default_document_manager');
    }

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient([], []);
        $this->dm->getConnection()->dropDatabase('pipes');
    }

    /**
     * @param object $document
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

        return (object) [
            'status'  => $response->getStatusCode(),
            'content' => Json::decode($response->getContent()),
        ];
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
        $this->client->request('POST', $url, $parameters, [], [], $content ? Json::encode($content) : '');
        $response = $this->client->getResponse();

        return (object) [
            'status'  => $response->getStatusCode(),
            'content' => Json::decode($response->getContent()),
        ];
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
        $this->client->request('PUT', $url, $parameters, [], [], $content ? Json::encode($content) : '');
        $response = $this->client->getResponse();

        return (object) [
            'status'  => $response->getStatusCode(),
            'content' => Json::decode($response->getContent()),
        ];
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

        return (object) [
            'status'  => $response->getStatusCode(),
            'content' => Json::decode($response->getContent()),
        ];
    }

}