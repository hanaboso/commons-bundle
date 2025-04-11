<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Exception;

use Hanaboso\Utils\Exception\PipesFrameworkExceptionAbstract;

/**
 * Class NodeException
 *
 * @package Hanaboso\CommonsBundle\Exception
 */
final class NodeException extends PipesFrameworkExceptionAbstract
{

    public const int INVALID_TYPE                        = self::OFFSET + 1;
    public const int INVALID_HANDLER                     = self::OFFSET + 2;
    public const int NODE_NOT_FOUND                      = self::OFFSET + 3;
    public const int DISALLOWED_ACTION_ON_NON_EVENT_NODE = self::OFFSET + 4;

    protected const int OFFSET = 2_300;

}
