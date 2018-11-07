<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Enum;

use Hanaboso\CommonsBundle\Exception\EnumException;

/**
 * Class EnumAbstract
 *
 * @package Hanaboso\CommonsBundle\Enum
 */
abstract class EnumAbstract
{

    /**
     * @var string[]
     */
    protected static $choices = [];

    /**
     * @return string[]
     */
    public static function getChoices(): array
    {
        return static::$choices;
    }

    /**
     * @param string $val
     *
     * @return string
     * @throws EnumException
     */
    public static function isValid(string $val): string
    {
        if (!array_key_exists($val, static::$choices)) {
            throw new EnumException(
                sprintf('[%s] is not a valid option from [%s].', $val, static::class),
                EnumException::INVALID_CHOICE
            );
        }

        return $val;
    }

}