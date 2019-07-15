<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Database\Locator;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManager;

/**
 * Interface DatabaseManagerLocatorInterface
 *
 * @package Hanaboso\CommonsBundle\Database\Locator
 */
interface DatabaseManagerLocatorInterface
{

    /**
     * @return DocumentManager|null
     */
    public function getDm(): ?DocumentManager;

    /**
     * @return EntityManager|null
     */
    public function getEm(): ?EntityManager;

}