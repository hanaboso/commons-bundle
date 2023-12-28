<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\Database\Filters\ODM;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Hanaboso\CommonsBundle\Database\Traits\Document\DeletedTrait;
use Hanaboso\CommonsBundle\Database\Traits\Document\IdTrait;

/**
 * Class TestDocument
 *
 * @package CommonsBundleTests\Integration\Database\Filters\ODM
 */
#[ODM\Document]
class TestDocument
{

    use IdTrait;
    use DeletedTrait;

    /**
     * @var string
     *
     */
    #[ODM\Field(type: 'string')]
    protected string $name;

    /**
     * TestDocument constructor.
     */
    public function __construct()
    {
        $this->name = '';
    }

    /**
     * @param string $name
     *
     * @return TestDocument
     */
    public function setName(string $name): self
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
