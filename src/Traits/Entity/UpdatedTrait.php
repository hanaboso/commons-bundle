<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Traits\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Hanaboso\CommonsBundle\Exception\DateTimeException;
use Hanaboso\CommonsBundle\Utils\DateTimeUtils;

/**
 * Trait UpdatedTrait
 *
 * @package Hanaboso\CommonsBundle\Traits\Entity
 */
trait UpdatedTrait
{

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * @return DateTime
     */
    public function getUpdated(): DateTime
    {
        return $this->updated;
    }

    /**
     * @ORM\PreUpdate()
     * @throws DateTimeException
     */
    public function preUpdate(): void
    {
        $this->updated = DateTimeUtils::getUtcDateTime();
    }

}
