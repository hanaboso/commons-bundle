<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Redirect;

/**
 * Class Redirect
 *
 * @package Hanaboso\CommonsBundle\Redirect
 * @codeCoverageIgnore
 */
final class Redirect implements RedirectInterface
{

    /**
     * @param string $url
     */
    public function make(string $url): void
    {
        header(sprintf('Location: %s', $url));
        exit;
    }

}
