<?php declare(strict_types=1);

namespace Tests\Integration\Database\Filters\ODM;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Hanaboso\CommonsBundle\Traits\Document\DeletedTrait;
use Hanaboso\CommonsBundle\Traits\Document\IdTrait;

/**
 * Class TestDocument
 *
 * @package Tests\Integration\Database\Filters\ODM
 *
 * @ODM\Document()
 */
class TestDocument
{

    use IdTrait;
    use DeletedTrait;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $name;

    /**
     * @param string $name
     *
     * @return TestDocument
     */
    public function setName(string $name): TestDocument
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

}
