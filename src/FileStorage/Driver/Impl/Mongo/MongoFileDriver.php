<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo;

use Doctrine\ODM\MongoDB\Repository\GridFSRepository;
use Hanaboso\CommonsBundle\Exception\FileStorageException;
use Hanaboso\CommonsBundle\FileStorage\Driver\FileStorageDriverAbstract;
use Hanaboso\CommonsBundle\FileStorage\Dto\FileInfoDto;
use Throwable;

/**
 * Class MongoFileDriver
 *
 * @package Hanaboso\CommonsBundle\FileStorage\Driver\Impl\Mongo
 */
final class MongoFileDriver extends FileStorageDriverAbstract
{

    /**
     * @param string      $content
     * @param string|null $filename
     *
     * @return FileInfoDto
     * @throws FileStorageException
     */
    public function save(string $content, ?string $filename = NULL): FileInfoDto
    {
        $filename = $this->generatePath($filename);
        /** @var resource $tmpFile */
        $tmpFile = tmpfile();
        fwrite($tmpFile, $content);
        fseek($tmpFile, 0);

        /** @var GridFSRepository $repository */
        $repository = $this->dm->getRepository(FileMongo::class);
        /** @var FileMongo $file */
        $file = $repository->uploadFromStream($filename, $tmpFile);
        try {
            $this->dm->persist($file);
            $this->dm->flush();
        } catch (Throwable $t) {
            throw new FileStorageException($t->getMessage(), $t->getCode(), $t);
        } finally {
            fclose($tmpFile);
        }

        return new FileInfoDto($file->getId(), (string) $file->getLength());
    }

    /**
     * @param string $fileUrl
     *
     * @throws FileStorageException
     */
    public function delete(string $fileUrl): void
    {
        $file = $this->getDocument($fileUrl);

        try {
            $this->dm->remove($file);
            $this->dm->flush();
        } catch (Throwable $t) {
            throw new FileStorageException($t->getMessage(), $t->getCode(), $t);
        }
    }

    /**
     * @param string $fileUrl
     *
     * @return string
     * @throws FileStorageException
     */
    public function get(string $fileUrl): string
    {
        $file = $this->getDocument($fileUrl);

        /** @var GridFSRepository $repository */
        $repository = $this->dm->getRepository(FileMongo::class);
        $stream     = $repository->openDownloadStream($file->getId());
        try {
            $contents = stream_get_contents($stream) ?: '';
        } finally {
            fclose($stream);
        }

        return $contents;
    }

    /**
     * @param string $fileId
     *
     * @return FileMongo
     * @throws FileStorageException
     */
    private function getDocument(string $fileId): FileMongo
    {
        /** @var FileMongo|null $file */
        $file = $this->dm->getRepository(FileMongo::class)->find($fileId);

        if (!$file) {
            throw new FileStorageException(
                sprintf('File in Mongo with given id [%s] not found.', $fileId),
                FileStorageException::FILE_NOT_FOUND,
            );
        }

        return $file;
    }

}
