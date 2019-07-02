<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\MongoDBException;
use Hanaboso\CommonsBundle\Document\Category;

/**
 * Class CategoryRepository
 *
 * @package Hanaboso\CommonsBundle\Repository
 */
class CategoryRepository extends DocumentRepository
{

    /**
     * @param Category $category
     *
     * @throws MongoDBException
     */
    public function childrenLevelUp(Category $category): void
    {
        $this->createQueryBuilder()
            ->updateMany()
            ->field('parent')->equals($category->getId())
            ->field('parent')->set($category->getParent())
            ->getQuery()
            ->execute();
    }

}
