<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Database\Filters\ORM;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class DeletedFilter extends SQLFilter
{

    public const NAME = 'deleted';

    /**
     * @param ClassMetadata $metadata
     * @param string        $table
     *
     * @return string
     */
    public function addFilterConstraint(ClassMetadata $metadata, $table): string
    {
        if ($metadata->getReflectionClass()->hasProperty(self::NAME)) {
            return sprintf('%s.%s = 0', $table, self::NAME);
        }

        return '';
    }

}