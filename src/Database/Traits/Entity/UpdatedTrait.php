<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Database\Traits\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Hanaboso\Utils\Date\DateTimeUtils;
use Hanaboso\Utils\Exception\DateTimeException;

/**
 * Trait UpdatedTrait
 *
 * @package Hanaboso\CommonsBundle\Database\Traits\Entity
 */
trait UpdatedTrait
{

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected DateTime $updated;

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
