<?php declare(strict_types=1);

namespace Tests\Integration\Database\Filters\ODM;

use Exception;
use Hanaboso\CommonsBundle\Database\Filters\ODM\DeletedFilter;
use Tests\DatabaseTestCaseAbstract;

/**
 * Class DeletedFilterTest
 *
 * @package Tests\Integration\Database\Filters\ODM
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
        $this->persistAndFlush($testDocument1);

        $testDocument2 = new TestDocument();
        $testDocument2->setName('example2');
        $testDocument2->setDeleted(TRUE);
        $this->persistAndFlush($testDocument2);

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
        $this->persistAndFlush($testDocument);

        $repository = $this->dm->getRepository(TestDocument::class);
        $this->dm->getFilterCollection()->disable(DeletedFilter::NAME);

        self::assertObjectHasAttribute('name', (object) $repository->findOneBy(['name' => 'example']));
    }

}