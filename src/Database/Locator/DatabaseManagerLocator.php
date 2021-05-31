<?php declare(strict_types=1);

namespace Hanaboso\CommonsBundle\Database\Locator;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManager;
use LogicException;

/**
 * Class DatabaseManagerLocator
 *
 * @package Hanaboso\CommonsBundle\Database\Locator
 */
final class DatabaseManagerLocator implements DatabaseManagerLocatorInterface
{

    /**
     * DatabaseManagerLocator constructor.
     *
     * @param DocumentManager|null $documentManager
     * @param EntityManager|null   $entityManager
     * @param string               $type
     */
    public function __construct(
        private ?DocumentManager $documentManager,
        private ?EntityManager $entityManager,
        private string $type,
    )
    {
    }

    /**
     * @return DocumentManager|EntityManager
     */
    public function get(): DocumentManager|EntityManager
    {
        $manager = NULL;
        if ($this->type === 'ODM') {
            $manager = $this->getDm();
        } else {
            if ($this->type === 'ORM') {
                $manager = $this->getEm();
            }
        }

        if (!$manager) {
            throw new LogicException('Database manager not found.');
        }

        return $manager;
    }

    /**
     * @return DocumentManager|null
     */
    public function getDm(): ?DocumentManager
    {
        return $this->documentManager;
    }

    /**
     * @return EntityManager|null
     */
    public function getEm(): ?EntityManager
    {
        return $this->entityManager;
    }

}
