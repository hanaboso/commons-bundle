<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\Database\Filters\ORM;

use Doctrine\ORM\Mapping as ORM;
use Hanaboso\CommonsBundle\Database\Traits\Entity\IdTrait;

/**
 * Class TestEntityNoDeletedProp
 *
 * @package CommonsBundleTests\Integration\Database\Filters\ORM
 */
#[ORM\Entity()]
class TestEntityNoDeletedProp
{

    use IdTrait;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string')]
    protected string $name;

    /**
     * TestEntityNoDeletedProp constructor.
     */
    public function __construct()
    {
        $this->name = '';
    }

    /**
     * @param string $name
     *
     * @return TestEntityNoDeletedProp
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
