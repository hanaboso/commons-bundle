<?php declare(strict_types=1);

namespace Tests\Integration\Database\Filters\ORM;

use Doctrine\ORM\Mapping as ORM;
use Hanaboso\CommonsBundle\Traits\Entity\DeletedTrait;
use Hanaboso\CommonsBundle\Traits\Entity\IdTrait;

/**
 * Class TestEntity
 *
 * @package Tests\Integration\Database\Filters\ORM
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
    protected $name;

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