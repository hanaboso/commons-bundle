<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\Database\Filters\ORM;

use Doctrine\ORM\Mapping as ORM;
use Hanaboso\CommonsBundle\Database\Traits\Entity\DeletedTrait;
use Hanaboso\CommonsBundle\Database\Traits\Entity\IdTrait;

/**
 * Class TestEntity
 *
 * @package CommonsBundleTests\Integration\Database\Filters\ORM
 *
 * @ORM\Entity()
 */
class TestEntity
{

    use IdTrait;
    use DeletedTrait;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected string $name;

    /**
     * TestEntity constructor.
     */
    public function __construct()
    {
        $this->name = '';
    }

    /**
     * @param string $name
     *
     * @return TestEntity
     */
    public function setName(string $name): TestEntity
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
