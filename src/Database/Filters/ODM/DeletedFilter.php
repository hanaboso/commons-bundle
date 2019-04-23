<?php

namespace Hanaboso\CommonsBundle\Database\Filters\ODM;

use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Query\Filter\BsonFilter;

class DeletedFilter extends BsonFilter
{
    public function addFilterCriteria(ClassMetadata $targetDocument)
    {

    }

}