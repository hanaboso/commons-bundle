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
     * @var string
     */
    protected $val;

    /**
     * @var string[]
     */
    protected static $choices = [];

    /**
     * EnumAbstract constructor.
     *
     * @param string $val
     * @throws EnumException
     */
    function __construct(string $val)
    {
        if (!self::isValid($val)) {
            throw new EnumException(
                sprintf('[%s] is not a valid option from [%s].', $val, __CLASS__),
                EnumException::INVALID_CHOICE
            );
        }
        $this->val = $val;
    }

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
     * @return bool
     */
    public static function isValid(string $val): bool
    {
        return array_key_exists($val, static::$choices);
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->val;
    }

}