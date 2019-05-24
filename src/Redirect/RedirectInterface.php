<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Redirect;

/**
 * Interface RedirectInterface
 *
 * @package Hanaboso\CommonsBundle\Redirect
 */
interface RedirectInterface
{

    /**
     * @param string $url
     */
    public function make(string $url): void;

}
