<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\Database\Filters\ORM;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use Exception;
use Hanaboso\CommonsBundle\Database\Filters\ORM\DeletedFilter;

/**
 * Class DeletedFilterTest
 *
 * @package CommonsBundleTests\Integration\Database\Filters\ORM
 */
final class DeletedFilterTest extends DatabaseTestCaseAbstract
{

    /**
     * @covers \Hanaboso\CommonsBundle\Database\Filters\ORM\DeletedFilter::addFilterConstraint
     *
     * @throws Exception
     */
    public function testAddFilterConstraint(): void
    {
        $this->clearMysql();

        $testEntity1 = new TestEntity();
        $testEntity1->setName('example1');
        $testEntity1->setDeleted(FALSE);
        $this->pfe($testEntity1);

        $testEntity2 = new TestEntity();
        $testEntity2->setName('example2');
        $testEntity2->setDeleted(TRUE);
        $this->pfe($testEntity2);

        $repository = $this->em->getRepository(TestEntity::class);

        self::assertTrue(property_exists((object) $repository->findOneBy(['name' => 'example1']), 'name'));
        self::assertNull($repository->findOneBy(['name' => 'example2']));
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Database\Filters\ORM\DeletedFilter
     *
     * @throws Exception
     */
    public function testDisableFilter(): void
    {
        $testEntity = new TestEntity();
        $testEntity->setName('example');
        $testEntity->setDeleted(TRUE);
        $this->pfe($testEntity);
        $repository = $this->em->getRepository(TestEntity::class);

        $this->em->getFilters()->disable(DeletedFilter::NAME);

        self::assertTrue(property_exists((object) $repository->findOneBy(['name' => 'example']), 'name'));
    }

    /**
     * @covers \Hanaboso\CommonsBundle\Database\Filters\ORM\DeletedFilter::addFilterConstraint
     * @throws Exception
     */
    public function testAddDeletedFilterConstraint(): void
    {
        $testEntity = new TestEntityNoDeletedProp();
        $testEntity->setName('example');
        $this->pfe($testEntity);

        $filter = $this->em
            ->getFilters()
            ->getFilter(DeletedFilter::NAME)
            ->addFilterConstraint(
                $this->em->getClassMetadata(TestEntityNoDeletedProp::class),
                'test_entity_no_deleted_prop',
            );

        self::assertEquals('', $filter);
    }

}
