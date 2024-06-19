<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\Database\Traits\Document;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class DocumentTraitsTest
 *
 * @package CommonsBundleTests\Integration\Database\Traits\Document
 */
#[CoversClass(TestDocumentTrait::class)]
final class DocumentTraitsTest extends DatabaseTestCaseAbstract
{

    /**
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

        $document->getCreated();
        $document->getUpdated();
        $document->getId();
        self::assertFalse($document->isDeleted());
    }

}
