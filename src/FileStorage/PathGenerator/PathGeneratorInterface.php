<?php declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: david.horacek
 * Date: 8/21/17
 * Time: 11:22 AM
 */

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