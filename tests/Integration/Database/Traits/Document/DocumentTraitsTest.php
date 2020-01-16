<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\Database\Traits\Document;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use DateTime;
use Exception;

/**
 * Class DocumentTraitsTest
 *
 * @package CommonsBundleTests\Integration\Database\Traits\Document
 */
final class DocumentTraitsTest extends DatabaseTestCaseAbstract
{

    /**
     * @covers \CommonsBundleTests\Integration\Database\Traits\Document\TestDocumentTrait::getCreated
     * @covers \CommonsBundleTests\Integration\Database\Traits\Document\TestDocumentTrait::getUpdated
     * @covers \CommonsBundleTests\Integration\Database\Traits\Document\TestDocumentTrait::getId
     * @covers \CommonsBundleTests\Integration\Database\Traits\Document\TestDocumentTrait::setDeleted
     * @covers \CommonsBundleTests\Integration\Database\Traits\Document\TestDocumentTrait::isDeleted
     * @covers \CommonsBundleTests\Integration\Database\Traits\Document\TestDocumentTrait::preUpdate
     *
     * @throws Exception
     */
    public function testDocumentTraits(): void
    {
        $this->pfd((new TestDocumentTrait())->setName('document1'));

        $repository = $this->dm->getRepository(TestDocumentTrait::class);
        /** @var TestDocumentTrait $document */
        $document = $repository->findOneBy(['name' => 'document1']);
        $document->setDeleted(FALSE);
        $document->preUpdate();

        self::assertInstanceOf(DateTime::class, $document->getCreated());
        self::assertInstanceOf(DateTime::class, $document->getUpdated());
        self::assertIsString($document->getId());
        self::assertFalse($document->isDeleted());
    }

}
