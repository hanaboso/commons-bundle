<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Traits\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait CreatedTrait
 *
 * @package Hanaboso\CommonsBundle\Traits\Entity
 */
trait CreatedTrait
{

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @return DateTime
     */
    public function getCreated(): DateTime
    {
        return $this->created;
    }

}
