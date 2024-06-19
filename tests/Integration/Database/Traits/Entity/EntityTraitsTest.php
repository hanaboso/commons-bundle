<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\Database\Traits\Entity;

use CommonsBundleTests\DatabaseTestCaseAbstract;
use Exception;
use Hanaboso\Utils\Exception\DateTimeException;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class EntityTraitsTest
 *
 * @package CommonsBundleTests\Integration\Database\Traits\Entity
 */
#[CoversClass(TestEntityTrait::class)]
final class EntityTraitsTest extends DatabaseTestCaseAbstract
{

    /**
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

        $entity->getCreated();
        $entity->getUpdated();
        $entity->getId();
        self::assertFalse($entity->isDeleted());
    }

}
