<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Database\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait DeletedTrait
 *
 * @package Hanaboso\CommonsBundle\Database\Traits\Entity
 */
trait DeletedTrait
{

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
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
