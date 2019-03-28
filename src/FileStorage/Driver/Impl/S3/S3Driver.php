<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\FileStorage\Driver\Impl\S3;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Doctrine\ODM\MongoDB\DocumentManager;
use Hanaboso\CommonsBundle\Exception\FileStorageException;
use Hanaboso\CommonsBundle\FileStorage\Driver\FileStorageDriverAbstract;
use Hanaboso\CommonsBundle\FileStorage\Dto\FileInfoDto;
use Hanaboso\CommonsBundle\FileStorage\PathGenerator\PathGeneratorInterface;

/**
 * Class S3Driver
 *
 * @package Hanaboso\CommonsBundle\FileStorage\Driver\Impl\S3
 */
class S3Driver extends FileStorageDriverAbstract
{

    private const BUCKET_FIELD = 'Bucket';
    private const KEY_FIELD    = 'Key';
    private const BODY_FIELD   = 'Body';
    private const META_FIELD   = 'Metadata';

    /**
     * @var S3Client
     */
    private $client;

    /**
     * @var string
     */
    private $bucket;

    /**
     * S3Driver constructor.
     *
     * @param DocumentManager        $dm
     * @param PathGeneratorInterface $generator
     * @param S3Client               $client
     * @param string                 $bucket
     */
    public function __construct(DocumentManager $dm, PathGeneratorInterface $generator, S3Client $client, string $bucket)
    {
        parent::__construct($dm, $generator);
        $this->client = $client;
        $this->bucket = $bucket;
    }

    /**
     * @param string      $content
     * @param string|null $filename
     *
     * @return FileInfoDto
     * @throws FileStorageException
     */
    public function save(string $content, ?string $filename = NULL): FileInfoDto
    {
        $path = $this->generatePath($filename);

        try {
            $args                   = $this->getBasicArgs($path);
            $args[self::BODY_FIELD] = $content;
            $args[self::META_FIELD] = [];

            $result = $this->client->putObject($args);

            $innerFile = new FileInfoDto($result['ObjectUrl'], $result['ObjectSize']);
        } catch (S3Exception $e) {
            throw new FileStorageException(
                sprintf("Cannot write file '%s': %s", $path, $e->getMessage()),
                $e->getCode(),
                $e
            );
        }

        return $innerFile;
    }

    /**
     * @param string $fileUrl
     */
    public function delete(string $fileUrl): void
    {
        $this->client->deleteObject($this->getBasicArgs($fileUrl));
    }

    /**
     * @param string $fileUrl
     *
     * @return string
     * @throws FileStorageException
     */
    public function get(string $fileUrl): string
    {
        try {
            $result = $this->client->getObject($this->getBasicArgs($fileUrl));
            return $result->get(self::BODY_FIELD)->getContents();
        } catch (S3Exception $e) {
            throw new FileStorageException(
                sprintf("Cannot read file '%s': %s", $fileUrl, $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @param string $path
     *
     * @return array
     */
    private function getBasicArgs(string $path): array
    {
        return [
            self::BUCKET_FIELD => $this->bucket,
            self::KEY_FIELD    => $path,
        ];
    }

}