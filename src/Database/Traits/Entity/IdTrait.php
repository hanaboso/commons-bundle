<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Database\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait IdTrait
 *
 * @package Hanaboso\CommonsBundle\Database\Traits\Entity
 */
trait IdTrait
{

    /**
     * @var string
     */
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Id()]
    #[ORM\Column(type: 'bigint', nullable: FALSE, options: ['unsigned' => TRUE])]
    protected string $id;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

}
