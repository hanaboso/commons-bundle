<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\Database\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hanaboso\CommonsBundle\Database\Traits\Entity\CreatedTrait;
use Hanaboso\CommonsBundle\Database\Traits\Entity\DeletedTrait;
use Hanaboso\CommonsBundle\Database\Traits\Entity\IdTrait;
use Hanaboso\CommonsBundle\Database\Traits\Entity\UpdatedTrait;
use Hanaboso\Utils\Date\DateTimeUtils;
use Hanaboso\Utils\Exception\DateTimeException;

/**
 * Class TestEntityTrait
 *
 * @package CommonsBundleTests\Integration\Database\Traits\Entity
 *
 * @ORM\Entity()
 */
class TestEntityTrait
{

    use IdTrait;
    use DeletedTrait;
    use UpdatedTrait;
    use CreatedTrait;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected string $name;

    /**
     * TestEntityTrait constructor.
     *
     * @throws DateTimeException
     */
    public function __construct()
    {
        $this->name    = '';
        $this->created = DateTimeUtils::getUtcDateTime();
        $this->updated = DateTimeUtils::getUtcDateTime();
    }

    /**
     * @param string $name
     *
     * @return TestEntityTrait
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
