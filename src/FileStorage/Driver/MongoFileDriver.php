<?php declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: david.horacek
 * Date: 8/21/17
 * Time: 1:44 PM
 */

namespace Hanaboso\CommonsBundle\FileStorage\Driver;

use Doctrine\MongoDB\GridFSFile;
use Hanaboso\CommonsBundle\Exception\FileStorageException;
use Hanaboso\CommonsBundle\FileStorage\Dto\FileInfoDto;

/**
 * Class MongoFileDriver
 *
 * @package Hanaboso\CommonsBundle\FileStorage\Driver
 */
class MongoFileDriver extends FileStorageDriverAbstract
{

    /**
     * @param string      $content
     * @param null|string $filename
     *
     * @return FileInfoDto
     */
    public function save(string $content, ?string $filename = NULL): FileInfoDto
    {
        $filename = $this->generatePath($filename);

        $gridFile = new GridFSFile();
        $gridFile->setBytes($content);

        $file = new FileMongo();
        $file
            ->setContent($gridFile)
            ->setFilename($filename);

        $this->dm->persist($file);
        $this->dm->flush($file);

        return new FileInfoDto($file->getId(), (string) ($file->getContent()->getSize()));
    }

    /**
     * @param string $fileId
     *
     * @throws FileStorageException
     */
    public function delete(string $fileId): void
    {
        /** @var FileMongo $file */
        $file = $this->getDocument($fileId);

        $this->dm->remove($file);
        $this->dm->flush();
    }

    /**
     * @param string $fileId
     *
     * @return string
     * @throws FileStorageException
     */
    public function get(string $fileId): string
    {
        $file = $this->getDocument($fileId);

        return $file->getContent()->getBytes() ?? '';
    }

    /**
     * @param string $fileId
     *
     * @return FileMongo
     * @throws FileStorageException
     */
    private function getDocument(string $fileId): FileMongo
    {
        /** @var FileMongo $file */
        $file = $this->dm->getRepository(FileMongo::class)->find($fileId);

        if (!$file) {
            throw new FileStorageException(
                sprintf('File in Mongo with given id [%s] not found.', $fileId),
                FileStorageException::FILE_NOT_FOUND
            );
        }

        return $file;
    }

}