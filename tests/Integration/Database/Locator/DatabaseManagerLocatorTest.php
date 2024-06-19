<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\Database\Locator;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManager;
use Exception;
use Hanaboso\CommonsBundle\Database\Locator\DatabaseManagerLocator;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class DatabaseManagerLocatorTest
 *
 * @package CommonsBundleTests\Integration\Database\Locator
 */
#[CoversClass(DatabaseManagerLocator::class)]
final class DatabaseManagerLocatorTest extends DatabaseTestCaseAbstract
{

    /**
     * @throws Exception
     */
    public function testConnectDocumentManager(): void
    {
        /** @var DocumentManager $documentManager */
        $documentManager = self::getContainer()->get('doctrine_mongodb.odm.default_document_manager');
        self::assertNotEmpty($documentManager->getClient()->listDatabases());
    }

    /**
     * @throws Exception
     */
    public function testConnectEntityManager(): void
    {
        /** @var EntityManager $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.default_entity_manager');

        self::assertNotEmpty(
            $entityManager->getConnection()->executeQuery('SHOW DATABASES;')->fetchAllAssociative(),
        );
    }

    /**
     * @throws Exception
     */
    public function testGet(): void
    {
        $entityManager   = self::getContainer()->get('doctrine.orm.default_entity_manager');
        $documentManager = self::getContainer()->get('doctrine_mongodb.odm.default_document_manager');

        $manager = new DatabaseManagerLocator($documentManager, NULL, 'ODM');
        self::assertInstanceOf(DocumentManager::class, $manager->get());

        $manager = new DatabaseManagerLocator(NULL, $entityManager, 'ORM');
        self::assertInstanceOf(EntityManager::class, $manager->get());

        self::expectException(LogicException::class);
        (new DatabaseManagerLocator(NULL, NULL, ''))->get();
    }

}
