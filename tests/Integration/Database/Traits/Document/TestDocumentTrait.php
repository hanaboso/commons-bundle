<?php declare(strict_types=1);

namespace CommonsBundleTests\Integration\Database\Traits\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Hanaboso\CommonsBundle\Database\Traits\Document\CreatedTrait;
use Hanaboso\CommonsBundle\Database\Traits\Document\DeletedTrait;
use Hanaboso\CommonsBundle\Database\Traits\Document\IdTrait;
use Hanaboso\CommonsBundle\Database\Traits\Document\UpdatedTrait;
use Hanaboso\Utils\Date\DateTimeUtils;
use Hanaboso\Utils\Exception\DateTimeException;

/**
 * Class TestDocumentTrait
 *
 * @package CommonsBundleTests\Integration\Database\Traits\Document
 */
#[ODM\Document]
class TestDocumentTrait
{

    use IdTrait;
    use DeletedTrait;
    use CreatedTrait;
    use UpdatedTrait;

    /**
     * @var string
     */
    #[ODM\Field(type: 'string')]
    protected string $name;

    /**
     * TestDocumentTrait constructor.
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
     * @return TestDocumentTrait
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
