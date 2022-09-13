<?php

declare(strict_types=1);

namespace App\Service;

use App\Models\Pagination;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

/**
 * Create pager and instantiate Pagination model class which can be used in twig template.
 */
final class PaginationManager
{
    public const MAX_ITEMS_PER_PAGE     = 100;
    public const DEFAULT_ITEMS_PER_PAGE = 10;

    /**
     * Return Pagination object with safe values.
     *
     * It is possible to limit which parameters are sortable with $sortFieldsWhitelist. If array key is a string, the
     * key will be used as the query parameter and value as parameter name for the query builder, if it's an int it will
     * be the same for both. Useful to simplify query parameters or hide implementation.
     * eg:
     *      $sortFieldsWhitelist = ['post.createdAt', 'author' => 'author.username']
     *      ?sort=post.created and query builder parameter will be the same, 'post.createdAt'.
     *      ?sort=author&direction=desc and query builder parameter will be 'author.username'
     * If $sortField is not in $sortFieldsWhitelist and whitelist is not empty, sort field value will be null.
     *
     * @param QueryBuilder              $queryBuilder
     * @param string                    $routeName
     * @param int                       $page
     * @param int                       $itemsPerPage
     * @param string|null               $sortField
     * @param string                    $sortOrder
     * @param array<int|string, string> $sortFieldsWhitelist
     * @param array<string, mixed>      $routeParams
     *
     * @return Pagination
     */
    public function generate(QueryBuilder $queryBuilder,
                             string       $routeName,
                             int          $page = 1,
                             int          $itemsPerPage = self::DEFAULT_ITEMS_PER_PAGE,
                             ?string      $sortField = null,
                             string       $sortOrder = Pagination::SORT_DIRECTION_ASC,
                             array        $sortFieldsWhitelist = [],
                             array        $routeParams = []): Pagination
    {
        $sortFieldQbName = $this->getQueryBuilderSortFieldName($sortField, $sortFieldsWhitelist);

        if ($sortOrder) {
            $sortOrder = strtolower($sortOrder);
            $sortOrder = in_array($sortOrder, [Pagination::SORT_DIRECTION_ASC, Pagination::SORT_DIRECTION_DESC])
                ? $sortOrder
                : Pagination::SORT_DIRECTION_ASC;
        }

        if ($sortFieldQbName) {
            $queryBuilder->orderBy($sortFieldQbName, $sortOrder);
        }

        $pager = new Pagerfanta(new QueryAdapter($queryBuilder));
        $pager->setMaxPerPage($itemsPerPage > self::MAX_ITEMS_PER_PAGE ? self::DEFAULT_ITEMS_PER_PAGE : $itemsPerPage);
        $pager->setCurrentPage($page);

        return new Pagination(
            $pager,
            $routeName,
            $routeParams,
            $sortFieldQbName ? $sortField : null,
            $sortOrder,
        );
    }

    /**
     * Returns query builder true parameter name instead of key used if needed.
     *
     * @param string|null               $sortField
     * @param array<string|int, string> $sortFieldsWhitelist
     *
     * @return string|null
     */
    private function getQueryBuilderSortFieldName(?string $sortField, array $sortFieldsWhitelist): ?string
    {
        if (empty($sortFieldsWhitelist)) {
            return $sortField;
        }

        foreach ($sortFieldsWhitelist as $k => $v) {
            if (is_int($k) && $v === $sortField) {
                return $sortField;
            }
            if (is_string($k) && $k === $sortField) {
                return $v;
            }
        }

        return null;
    }
}
