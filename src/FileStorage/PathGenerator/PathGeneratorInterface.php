<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\FileStorage\PathGenerator;

/**
 * Interface PathGeneratorInterface
 *
 * @package Hanaboso\CommonsBundle\FileStorage\PathGenerator
 */
interface PathGeneratorInterface
{

    /**
     * @param string|null $filename
     *
     * @return string
     */
    public function generate(?string $filename): string;

}
