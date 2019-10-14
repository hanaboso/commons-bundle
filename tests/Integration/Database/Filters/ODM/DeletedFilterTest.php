<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\Database\Filters\ODM;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Database\Filters\ODM\DeletedFilter;

/**
 * Class DeletedFilterTest
 *
 * @package CommonsBundleTests\Integration\Database\Filters\ODM
 */
final class DeletedFilterTest extends DatabaseTestCaseAbstract
{

    /**
     * @throws Exception
     */
    public function testAddFilterCriteria(): void
    {
        $testDocument1 = new TestDocument();
        $testDocument1->setName('example1');
        $testDocument1->setDeleted(FALSE);
        $this->pfd($testDocument1);

        $testDocument2 = new TestDocument();
        $testDocument2->setName('example2');
        $testDocument2->setDeleted(TRUE);
        $this->pfd($testDocument2);

        $repository = $this->dm->getRepository(TestDocument::class);

        self::assertObjectHasAttribute('name', (object) $repository->findOneBy(['name' => 'example1']));
        self::assertNull($repository->findOneBy(['name' => 'example2']));
    }

    /**
     * @throws Exception;
     */
    public function testDisableFilter(): void
    {
        $testDocument = new TestDocument();
        $testDocument->setName('example');
        $testDocument->setDeleted(TRUE);
        $this->pfd($testDocument);

        $repository = $this->dm->getRepository(TestDocument::class);
        $this->dm->getFilterCollection()->disable(DeletedFilter::NAME);

        self::assertObjectHasAttribute('name', (object) $repository->findOneBy(['name' => 'example']));
    }

}
