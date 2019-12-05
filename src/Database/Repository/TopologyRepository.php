<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Database\Repository;

use Doctrine\ODM\MongoDB\Iterator\Iterator;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Hanaboso\CommonsBundle\Database\Document\Category;
use Hanaboso\CommonsBundle\Database\Document\Topology;
use Hanaboso\CommonsBundle\Enum\TopologyStatusEnum;

/**
 * Class TopologyRepository
 *
 * @package Hanaboso\CommonsBundle\Database\Repository
 *
 * @phpstan-extends DocumentRepository<Topology>
 */
class TopologyRepository extends DocumentRepository
{

    /**
     * @param string $name
     *
     * @return Topology[]
     * @throws MongoDBException
     */
    public function getRunnableTopologies(string $name): array
    {
        /** @var Iterator<Topology> $result */
        $result = $this->createQueryBuilder()
            ->field('name')->equals($name)
            ->field('enabled')->equals(TRUE)
            ->field('deleted')->equals(FALSE)
            ->field('visibility')->equals(TopologyStatusEnum::PUBLIC)
            ->getQuery()->execute();

        return $result->toArray();
    }

    /**
     * @return integer
     * @throws MongoDBException
     */
    public function getTotalCount(): int
    {
        $result = $this->createQueryBuilder()
            ->field('deleted')->equals(FALSE)
            ->count()
            ->getQuery()->execute();

        if (is_scalar($result)) {
            return $result;
        }

        return 0;
    }

    /**
     * @param string $name
     *
     * @return int
     */
    public function getMaxVersion(string $name): int
    {
        /** @var Topology|null $result */
        $result = $this->createQueryBuilder()
            ->field('name')->equals($name)
            ->sort('version', 'DESC')
            ->limit(1)
            ->getQuery()->getSingleResult();

        if (!$result) {
            return 0;
        }

        return $result->getVersion();
    }

    /**
     * @param string $topologyName
     *
     * @return int
     * @throws MongoDBException
     */
    public function getTopologiesCountByName(string $topologyName): int
    {
        $result = $this->createQueryBuilder()
            ->field('name')->equals($topologyName)
            ->field('deleted')->equals(FALSE)
            ->count()
            ->getQuery()->execute();

        if (is_scalar($result)) {
            return $result;
        }

        return 0;
    }

    /**
     * @return Topology[]
     * @throws MongoDBException
     */
    public function getTopologies(): array
    {
        /** @var Iterator<Topology> $result */
        $result = $this->createQueryBuilder()
            ->field('visibility')->equals(TopologyStatusEnum::PUBLIC)
            ->field('deleted')->equals(FALSE)
            ->sort('version', 1)
            ->getQuery()->execute();
        /** @var Topology[] $results */
        $results = $result->toArray();

        $res = [];
        foreach ($results as $result) {
            $res[$result->getName()] = $result;
            unset($result);
        }

        return $res;
    }

    /**
     * @return Topology[]
     * @throws MongoDBException
     */
    public function getPublicEnabledTopologies(): array
    {
        /** @var Iterator<Topology> $result */
        $result = $this->createQueryBuilder()
            ->field('visibility')->equals(TopologyStatusEnum::PUBLIC)
            ->field('enabled')->equals(TRUE)
            ->field('deleted')->equals(FALSE)
            ->getQuery()
            ->execute();

        return $result->toArray();
    }

    /**
     * @param Category $category
     *
     * @return Topology[]
     */
    public function getTopologiesByCategory(Category $category): array
    {
        return $this->findBy(['category' => $category->getId(), 'deleted' => FALSE]);
    }

}
