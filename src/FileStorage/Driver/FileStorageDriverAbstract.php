<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\FileStorage\Driver;

use Doctrine\ODM\MongoDB\DocumentManager;
use Hanaboso\CommonsBundle\FileStorage\PathGenerator\PathGeneratorInterface;

/**
 * Class FileStorageDriverAbstract
 *
 * @package Hanaboso\CommonsBundle\FileStorage\Driver
 */
abstract class FileStorageDriverAbstract implements FileStorageDriverInterface
{

    /**
     * @var string
     */
    protected string $filePrefix;

    /**
     * FileStorageDriverAbstract constructor.
     *
     * @param DocumentManager        $dm
     * @param PathGeneratorInterface $pathGenerator
     */
    function __construct(protected DocumentManager $dm, protected PathGeneratorInterface $pathGenerator)
    {
        $this->filePrefix = '';
    }

    /**
     * @param PathGeneratorInterface $generator
     */
    public function setPathGenerator(PathGeneratorInterface $generator): void
    {
        $this->pathGenerator = $generator;
    }

    /**
     * @param string|null $filename
     *
     * @return string
     */
    protected function generatePath(?string $filename): string
    {
        return sprintf('%s%s', $this->filePrefix, $this->pathGenerator->generate($filename));
    }

}
