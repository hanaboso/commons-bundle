<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Database\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Index;
use Hanaboso\CommonsBundle\Database\Traits\Document\IdTrait;

/**
 * Class Category
 *
 * @package Hanaboso\CommonsBundle\Database\Document
 *
 * @MongoDB\Document(
 *     repositoryClass="Hanaboso\CommonsBundle\Database\Repository\CategoryRepository",
 *     indexes={
 *         @MongoDB\Index(keys={"name": "asc", "parent": "asc"}, unique="true")
 *     }
 * )
 */
class Category
{

    use IdTrait;

    /**
     * @var string
     *
     * @MongoDB\Field(type="string")
     */
    protected string $name;

    /**
     * @var string|null
     *
     * @MongoDB\Field(type="string")
     * @Index()
     */
    protected ?string $parent;

    /**
     * Category constructor.
     */
    public function __construct()
    {
        $this->name   = '';
        $this->parent = NULL;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Category
     */
    public function setName(string $name): Category
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getParent(): ?string
    {
        return $this->parent;
    }

    /**
     * @param string|null $parent
     *
     * @return Category
     */
    public function setParent(?string $parent): Category
    {
        $this->parent = $parent;

        return $this;
    }

}
