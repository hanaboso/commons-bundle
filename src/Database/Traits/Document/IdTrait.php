<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Database\Traits\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Trait IdTrait
 *
 * @package Hanaboso\CommonsBundle\Database\Traits\Document
 */
trait IdTrait
{

    /**
     * @var string
     *
     * @ODM\Id()
     */
    protected string $id;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

}
