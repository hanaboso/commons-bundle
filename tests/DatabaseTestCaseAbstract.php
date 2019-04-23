<?php declare(strict_types=1);

namespace Tests;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManager;
use Exception;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class DatabaseTestCaseAbstract
 *
 * @package Tests
 */
abstract class DatabaseTestCaseAbstract extends KernelTestCaseAbstract
{

    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var Session
     */
    protected $session;

    /**
     * DatabaseTestCaseAbstract constructor.
     *
     * @param null   $name
     * @param array  $data
     * @param string $dataName
     */
    public function __construct($name = NULL, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->session = new Session();
    }

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->dm->getConnection()->dropDatabase('pipes');
        $this->session->invalidate();
        $this->session->clear();

        /** @var Connection $connection */
        $connection = $this->em->getConnection();

        $parameters = $this->getProperty($connection, 'params');
        $this->setProperty($connection, 'params',
            array_merge($parameters, ['dbname' => $this->em->getConnection()->getDatabase()]));
        $tables = $connection->getSchemaManager()->listTableNames();
        $connection->exec('SET FOREIGN_KEY_CHECKS = 0;');
        foreach ($tables as $table) {
            $connection->exec(sprintf('TRUNCATE TABLE %s;', $table));
        }
        $connection->exec('SET FOREIGN_KEY_CHECKS = 1;');

    }

    /**
     * @param      $obj
     * @param bool $isEm
     *
     * @throws Exception
     */
    protected function persistAndFlush($obj, bool $isEm = FALSE): void
    {
        if ($isEm) {
            $this->em->persist($obj);
            $this->em->flush($obj);
        } else {
            $this->dm->persist($obj);
            $this->dm->flush($obj);
        }
    }

}
