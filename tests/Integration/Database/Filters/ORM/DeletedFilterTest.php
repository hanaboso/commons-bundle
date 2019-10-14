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
     * @throws Exception;
     */
    public function testAddFilterConstraint(): void
    {
        $testEntity1 = new TestEntity();
        $testEntity1->setName('example1');
        $testEntity1->setDeleted(FALSE);
        $this->pfe($testEntity1);

        $testEntity2 = new TestEntity();
        $testEntity2->setName('example2');
        $testEntity2->setDeleted(TRUE);
        $this->pfe($testEntity2);

        $repository = $this->em->getRepository(TestEntity::class);

        self::assertObjectHasAttribute('name', (object) $repository->findOneBy(['name' => 'example1']));
        self::assertNull($repository->findOneBy(['name' => 'example2']));
    }

    /**
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

        self::assertObjectHasAttribute('name', (object) $repository->findOneBy(['name' => 'example']));
    }

}
