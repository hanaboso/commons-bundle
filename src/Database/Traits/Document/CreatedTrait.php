<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Database\Traits\Document;

use DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Trait CreatedTrait
 *
 * @package Hanaboso\CommonsBundle\Database\Traits\Document
 */
trait CreatedTrait
{

    /**
     * @var DateTime
     */
    #[ODM\Field(type: 'date')]
    protected DateTime $created;

    /**
     * @return DateTime
     */
    public function getCreated(): DateTime
    {
        return $this->created;
    }

}
