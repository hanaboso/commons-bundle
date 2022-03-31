<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\Database\Filters\ODM;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Hanaboso\CommonsBundle\Database\Traits\Document\IdTrait;

/**
 * Class TestDocNoDeletedProp
 *
 * @package CommonsBundleTests\Integration\Database\Filters\ODM
 *
 * @ODM\Document()
 */
class TestDocNoDeletedProp
{

    use IdTrait;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected string $name;

    /**
     * TestDocNoDeletedProp constructor.
     */
    public function __construct()
    {
        $this->name = '';
    }

    /**
     * @param string $name
     *
     * @return TestDocNoDeletedProp
     */
    public function setName(string $name): TestDocNoDeletedProp
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
