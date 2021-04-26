<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Database\Filters\ORM;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

/**
 * Class DeletedFilter
 *
 * @package Hanaboso\CommonsBundle\Database\Filters\ORM
 */
final class DeletedFilter extends SQLFilter
{

    public const NAME = 'deleted';

    /**
     * @param ClassMetadata $targetEntity
     * @param string        $targetTableAlias
     *
     * @return string
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        if ($targetEntity->getReflectionClass()->hasProperty(self::NAME)) {
            return sprintf('%s.%s = 0', $targetTableAlias, self::NAME);
        }

        return '';
    }

}
