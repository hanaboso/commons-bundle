<?php declare(strict_types=1);

namespace Tests\Integration\Database\Locator;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManager;
use Exception;
use PDO;
use Tests\DatabaseTestCaseAbstract;

/**
 * Class DatabaseManagerLocatorTest
 *
 * @package Tests\Integration\Database\Locator
 */
final class DatabaseManagerLocatorTest extends DatabaseTestCaseAbstract
{

    /**
     *
     */
    public function testConnectDocumentManager(): void
    {
        /** @var DocumentManager $documentManager */
        $documentManager = self::$container->get('doctrine_mongodb.odm.default_document_manager');
        self::assertTrue(is_array($documentManager->getConnection()->listDatabases()));
    }

    /**
     * @throws Exception
     */
    public function testConnectEntityManager(): void
    {
        /** @var EntityManager $entityManager */
        $entityManager = self::$container->get('doctrine.orm.default_entity_manager');

        $query = $entityManager->getConnection()->query('SHOW DATABASES;');
        $query->execute();
        self::assertTrue(is_array($query->fetchAll(PDO::FETCH_OBJ)));
    }

}
