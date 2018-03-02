<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait IdTrait
 *
 * @package Hanaboso\CommonsBundle\Traits\Entity
 */
trait IdTrait
{

    /**
     * @var string
     *
     * @ORM\Column(type="bigint", nullable=false, options={"unsigned":true})
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

}