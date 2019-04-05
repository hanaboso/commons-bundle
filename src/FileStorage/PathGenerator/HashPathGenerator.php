<?php declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: david.horacek
 * Date: 8/21/17
 * Time: 2:32 PM
 */

namespace Hanaboso\CommonsBundle\FileStorage\PathGenerator;

use Exception;

/**
 * Class HashPathGenerator
 *
 * @package Hanaboso\CommonsBundle\FileStorage\PathGenerator
 */
class HashPathGenerator implements PathGeneratorInterface
{

    /**
     * @var int
     */
    private $levels = 2;

    /**
     * @var int
     */
    private $segment = 2;

    /**
     * @param string|null $filename
     *
     * @return string
     * @throws Exception
     */
    public function generate(?string $filename): string
    {
        $res = '';
        if (!$filename) {
            $filename = base_convert(bin2hex(random_bytes(16)), 16, 36);

            $chunks = (array) str_split($filename, $this->segment);
            for ($i = 0; $i < $this->levels; $i++) {
                $res .= sprintf('%s%s', array_shift($chunks), DIRECTORY_SEPARATOR);
            }
        }

        return sprintf('%s%s', $res, $filename);
    }

}
