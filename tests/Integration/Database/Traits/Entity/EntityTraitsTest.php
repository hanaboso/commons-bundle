<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\Database\Traits\Entity;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use DateTime;
use Exception;
use Hanaboso\Utils\Exception\DateTimeException;

/**
 * Class EntityTraitsTest
 *
 * @package CommonsBundleTests\Integration\Database\Traits\Entity
 */
final class EntityTraitsTest extends DatabaseTestCaseAbstract
{

    /**
     * @covers \CommonsBundleTests\Integration\Database\Traits\Entity\TestEntityTrait::preUpdate
     * @covers \CommonsBundleTests\Integration\Database\Traits\Entity\TestEntityTrait::isDeleted
     * @covers \CommonsBundleTests\Integration\Database\Traits\Entity\TestEntityTrait::setDeleted
     * @covers \CommonsBundleTests\Integration\Database\Traits\Entity\TestEntityTrait::getCreated
     * @covers \CommonsBundleTests\Integration\Database\Traits\Entity\TestEntityTrait::getId
     * @covers \CommonsBundleTests\Integration\Database\Traits\Entity\TestEntityTrait::getUpdated
     *
     * @throws DateTimeException
     * @throws Exception
     */
    public function testEntityTraits(): void
    {
        $this->pfe((new TestEntityTrait())->setName('document1'));

        $repository = $this->em->getRepository(TestEntityTrait::class);
        /** @var TestEntityTrait $entity */
        $entity = $repository->findOneBy(['name' => 'document1']);
        $entity->setDeleted(FALSE);
        $entity->preUpdate();

        self::assertInstanceOf(DateTime::class, $entity->getCreated());
        self::assertInstanceOf(DateTime::class, $entity->getUpdated());
        self::assertIsString($entity->getId());
        self::assertFalse($entity->isDeleted());
    }

}