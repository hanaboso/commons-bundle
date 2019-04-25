<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Database\Filters\ODM;

use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Query\Filter\BsonFilter;

/**
 * Class DeletedFilter
 *
 * @package Hanaboso\CommonsBundle\Database\Filters\ODM
 */
class DeletedFilter extends BsonFilter
{

    public const NAME = 'deleted';

    /**
     * @param ClassMetadata $targetDocument
     *
     * @return array
     */
    public function addFilterCriteria(ClassMetadata $targetDocument): array
    {
        if ($targetDocument->reflClass->hasProperty(self::NAME)) {
            return [self::NAME => FALSE];
        }

        return [];
    }

}