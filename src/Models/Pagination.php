<?php

declare(strict_types=1);

namespace App\Models;

use JetBrains\PhpStorm\Immutable;
use Pagerfanta\PagerfantaInterface;

#[immutable]
final class Pagination
{
    public const SORT_DIRECTION_ASC  = 'asc';
    public const SORT_DIRECTION_DESC = 'desc';

    /**
     * @param PagerfantaInterface<mixed> $pager
     * @param string                     $routeName
     * @param array<string, mixed>       $routeParams
     * @param string|null                $sortField
     * @param string|null                $sortOrder
     */
    public function __construct(
        public PagerfantaInterface $pager,
        public string              $routeName,
        public array               $routeParams,
        public ?string             $sortField = null,
        public ?string             $sortOrder = null,
    )
    {
    }
}
