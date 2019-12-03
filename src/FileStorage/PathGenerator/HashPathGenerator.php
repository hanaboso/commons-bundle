<?php declare(strict_types=1);

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
    private int $levels;

    /**
     * @var int
     */
    private int $segment;

    /**
     * HashPathGenerator constructor.
     */
    public function __construct()
    {
        $this->levels  = 2;
        $this->segment = 2;
    }

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
