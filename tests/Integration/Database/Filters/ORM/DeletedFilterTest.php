<?php

namespace Tests\Integration\Database\Filters\ORM;

use Hanaboso\CommonsBundle\Database\Filters\ORM\DeletedFilter;
use Tests\DatabaseTestCaseAbstract;

class DeletedFilterTest extends DatabaseTestCaseAbstract
{

    public function testAddFilterConstraint(): void
    {
        $testEntity = new TestEntity();
        $testEntity->setName('example');
        $testEntity->setDeleted(FALSE);
        $this->persistAndFlush($testEntity, TRUE);
        $repository = $this->em->getRepository(TestEntity::class);

        self::assertEquals('example', $repository->findOneBy(['name' => 'example'])->getName());

        $testEntity->setDeleted(TRUE);
        $this->persistAndFlush($testEntity, TRUE);

        self::assertNull($repository->findOneBy(['name' => 'example']));
    }

    public function testDisableFilter()
    {
        $testEntity = new TestEntity();
        $testEntity->setName('example1');
        $testEntity->setDeleted(TRUE);
        $this->persistAndFlush($testEntity, TRUE);
        $repository = $this->em->getRepository(TestEntity::class);

        $this->em->getFilters()->disable(DeletedFilter::NAME);

        self::assertNull($repository->findOneBy(['name' => 'example']));
    }

}