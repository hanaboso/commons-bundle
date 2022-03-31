<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\FileStorage\PathGenerator;

use Exception;
use Hanaboso\Utils\String\SafePathGenerator;

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
    protected int $levels;

    /**
     * @var int<1, max>
     */
    protected int $segment;

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
            $res = SafePathGenerator::generate($this->levels, $this->segment);
        }

        return sprintf('%s%s', $res, $filename);
    }

}
