<?php declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: radek.jirsa
 * Date: 17.8.17
 * Time: 16:29
 */

namespace Hanaboso\CommonsBundle\Redirect;

/**
 * Class Redirect
 *
 * @package Hanaboso\CommonsBundle\Redirect
 */
final class Redirect implements RedirectInterface
{

    /**
     * @param string $url
     */
    public function make(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

}