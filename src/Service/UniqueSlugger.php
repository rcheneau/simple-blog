<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\String\AbstractUnicodeString;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Extends Symfony slugger capacities to check in database if a slug is not already used. If so append number to
 * generated slug.
 */
final class UniqueSlugger
{
    private SluggerInterface       $slugger;
    private EntityManagerInterface $em;

    public function __construct(SluggerInterface $slugger, EntityManagerInterface $em)
    {
        $this->slugger = $slugger;
        $this->em      = $em;
    }

    /**
     * @param string $string      String to slugify.
     * @param string $entityClass Entity class to check from.
     * @param string $propertyName
     *
     * @return AbstractUnicodeString
     */
    public function uniqueSlugInEntity(string $string,
                                       string $entityClass,
                                       string $propertyName = 'slug'): AbstractUnicodeString
    {
        $slug = $this->slugger->slug($string);

        $last = $this->getLastNumberSlug($slug, $entityClass, $propertyName);

        if ($last === null) {
            return $slug;
        }

        return $slug->append('-', strval($last + 1));
    }

    private function getLastNumberSlug(AbstractUnicodeString $slug,
                                       string                $entityClass,
                                       string                $propertyName = 'slug'): int|null
    {
        $metadata  = $this->em->getClassMetadata($entityClass);
        $tableName = $metadata->getTableName();
        $fieldName = $metadata->getFieldName($propertyName);

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('n', 'n');

        $query = $this->em
            ->createNativeQuery(
                <<<SQL
                SELECT CASE WHEN (substring(e.$fieldName, '[^-]*$'))~'^\d+$' 
                           THEN substring(e.$fieldName, '[^-]*$')::int 
                           ELSE 0 
                       END AS n
                FROM $tableName e 
                WHERE e.$fieldName = ? OR e.$fieldName ~ ?
                ORDER BY n DESC
                LIMIT 1
                SQL,
                $rsm
            )->setParameter(1, $slug)
            ->setParameter(2, "$slug-[0-9]");

        try {
            /** @var array<string, int> $r */
            $r = $query->getSingleResult();
        } catch (NoResultException|NonUniqueResultException) {
            return null;
        }

        return $r['n'];
    }
}
