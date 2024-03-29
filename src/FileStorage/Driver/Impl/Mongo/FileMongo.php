<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo;

use DateTimeInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Hanaboso\CommonsBundle\Database\Traits\Document\IdTrait;
use Hanaboso\Utils\Date\DateTimeUtils;
use Hanaboso\Utils\Exception\DateTimeException;

/**
 * Class FileMongo
 *
 * @package Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo
 */
#[ODM\File(bucketName: 'files')]
class FileMongo
{

    use IdTrait;

    /**
     * @var string
     */
    #[ODM\File\Filename()]
    protected string $filename;

    /**
     * @var DateTimeInterface
     */
    #[ODM\File\UploadDate()]
    protected DateTimeInterface $uploadDate;

    /**
     * @var int
     */
    #[ODM\File\Length()]
    protected int $length;

    /**
     * @var int
     */
    #[ODM\File\ChunkSize()]
    protected int $chunkSize;

    /**
     * FileMongo constructor.
     *
     * @throws DateTimeException
     */
    public function __construct()
    {
        $this->uploadDate = DateTimeUtils::getUtcDateTime();
        $this->chunkSize  = 1_024 * 1_024;
        $this->length     = 0;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     *
     * @return FileMongo
     */
    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getUploadDate(): DateTimeInterface
    {
        return $this->uploadDate;
    }

    /**
     * @param DateTimeInterface $uploadDate
     *
     * @return FileMongo
     */
    public function setUploadDate(DateTimeInterface $uploadDate): self
    {
        $this->uploadDate = $uploadDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * @param int $length
     *
     * @return FileMongo
     */
    public function setLength(int $length): self
    {
        $this->length = $length;

        return $this;
    }

    /**
     * @return int
     */
    public function getChunkSize(): int
    {
        return $this->chunkSize;
    }

    /**
     * @param int $chunkSize
     *
     * @return FileMongo
     */
    public function setChunkSize(int $chunkSize): self
    {
        $this->chunkSize = $chunkSize;

        return $this;
    }

}
