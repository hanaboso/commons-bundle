<?php declare(strict_types=1);

namespace Tests;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class KernelTestCaseAbstract
 *
 * @package Tests
 */
abstract class KernelTestCaseAbstract extends KernelTestCase
{

    use PrivateTrait;

    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * KernelTestCaseAbstract constructor.
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
        $this->dm = self::$container->get('doctrine_mongodb.odm.default_document_manager');
        $this->em = self::$container->get('doctrine.orm.default_entity_manager');
    }

}
