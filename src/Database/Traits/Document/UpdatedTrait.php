<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Database\Traits\Document;

use DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Hanaboso\Utils\Date\DateTimeUtils;
use Hanaboso\Utils\Exception\DateTimeException;

/**
 * Trait UpdatedTrait
 *
 * @package Hanaboso\CommonsBundle\Database\Traits\Document
 */
trait UpdatedTrait
{

    /**
     * @var DateTime
     *
     * @ODM\Field(type="date")
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
     * @ODM\PreUpdate()
     * @throws DateTimeException
     */
    public function preUpdate(): void
    {
        $this->updated = DateTimeUtils::getUtcDateTime();
    }

}
