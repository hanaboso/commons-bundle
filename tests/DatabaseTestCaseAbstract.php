<?php declare(strict_types=1);

namespace CommonsBundleTests;

use Exception;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\DatabaseTestTrait;

/**
 * Class DatabaseTestCaseAbstract
 *
 * @package CommonsBundleTests
 */
abstract class DatabaseTestCaseAbstract extends KernelTestCaseAbstract
{

    use DatabaseTestTrait;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->dm = self::getContainer()->get('doctrine_mongodb.odm.default_document_manager');
        $this->em = self::getContainer()->get('doctrine.orm.default_entity_manager');
        $this->clearMongo();
        $this->clearMysql();
    }

}
