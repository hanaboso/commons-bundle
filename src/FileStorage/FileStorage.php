<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\FileStorage;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Hanaboso\CommonsBundle\Database\Locator\DatabaseManagerLocator;
use Hanaboso\CommonsBundle\Exception\FileStorageException;
use Hanaboso\CommonsBundle\FileStorage\Document\File;
use Hanaboso\CommonsBundle\FileStorage\Driver\FileStorageDriverLocator;
use Hanaboso\CommonsBundle\FileStorage\Dto\FileContentDto;
use Hanaboso\CommonsBundle\FileStorage\Dto\FileStorageDto;

/**
 * Class FileStorage
 *
 * @package Hanaboso\CommonsBundle\FileStorage
 */
final class FileStorage
{

    /**
     * @var EntityManager|DocumentManager
     */
    private DocumentManager|EntityManager $dm;

    /**
     * FileStorage constructor.
     *
     * @template T of File
     * @phpstan-param class-string<T>  $fileNamespace
     *
     * @param FileStorageDriverLocator $locator
     * @param DatabaseManagerLocator   $dm
     * @param string                   $fileNamespace
     */
    function __construct(
        private FileStorageDriverLocator $locator,
        DatabaseManagerLocator $dm,
        private string $fileNamespace
    )
    {
        $this->dm = $dm->get();
    }

    /**
     * @param FileContentDto $content
     *
     * @return FileInterface
     * @throws FileStorageException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws MongoDBException
     */
    public function saveFileFromContent(FileContentDto $content): FileInterface
    {
        $driver = $this->locator->get($content->getStorageType());
        $info   = $driver->save($content->getContent(), $content->getFilename());

        /** @var FileInterface $file */
        $file = new $this->fileNamespace();
        $file
            ->setFilename($content->getFilename() ?? $info->getUrl())
            ->setFileFormat($content->getFormat())
            ->setFileUrl($info->getUrl())
            ->setSize($info->getSize())
            ->setStorageType($content->getStorageType());

        $this->dm->persist($file);
        $this->dm->flush();

        return $file;
    }

    /**
     * @param FileInterface $file
     *
     * @return FileStorageDto
     * @throws FileStorageException
     */
    public function getFileStorage(FileInterface $file): FileStorageDto
    {
        $driver = $this->locator->get($file->getStorageType());

        return new FileStorageDto($file, $driver->get($file->getFileUrl()));
    }

    /**
     * @param FileInterface $file
     *
     * @throws FileStorageException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws MongoDBException
     */
    public function deleteFile(FileInterface $file): void
    {
        $driver = $this->locator->get($file->getStorageType());
        $driver->delete($file->getFileUrl());

        $this->dm->remove($file);
        $this->dm->flush();
    }

    /**
     * @param string $fileId
     *
     * @return FileInterface
     * @throws FileStorageException
     */
    public function getFileDocument(string $fileId): FileInterface
    {
        /** @var FileInterface|null $file */
        $file = $this->dm->getRepository($this->fileNamespace)->find($fileId);

        if (!$file) {
            throw new FileStorageException(
                sprintf('File with given id [%s] was not found.', $fileId),
                FileStorageException::FILE_NOT_FOUND
            );
        }

        return $file;
    }

}
