<?php declare(strict_types=1);

namespace Tests;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
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
        $this->em->getConnection();
    }

    /**
     * @param mixed $obj
     * @param bool  $isEm
     *
     * @throws ORMException
     * @throws OptimisticLockException
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
