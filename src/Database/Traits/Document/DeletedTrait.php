<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Database\Traits\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Trait DeletedTrait
 *
 * @package Hanaboso\CommonsBundle\Database\Traits\Document
 */
trait DeletedTrait
{

    /**
     * @var bool
     *
     * @ODM\Field(type="bool")
     */
    protected bool $deleted = FALSE;

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     *
     * @return $this
     */
    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

}
