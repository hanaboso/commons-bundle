<?php declare(strict_types=1);

namespace Tests\Integration\Database\Filters\ORM;

use Exception;
use Hanaboso\CommonsBundle\Database\Filters\ORM\DeletedFilter;
use Tests\DatabaseTestCaseAbstract;

/**
 * Class DeletedFilterTest
 *
 * @package Tests\Integration\Database\Filters\ORM
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
        $this->persistAndFlush($testEntity1, TRUE);

        $testEntity2 = new TestEntity();
        $testEntity2->setName('example2');
        $testEntity2->setDeleted(TRUE);
        $this->persistAndFlush($testEntity2, TRUE);

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
        $this->persistAndFlush($testEntity, TRUE);
        $repository = $this->em->getRepository(TestEntity::class);

        $this->em->getFilters()->disable(DeletedFilter::NAME);

        self::assertObjectHasAttribute('name', (object) $repository->findOneBy(['name' => 'example']));
    }

}