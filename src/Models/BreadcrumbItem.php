<?php

declare(strict_types=1);

namespace App\Models;

use JetBrains\PhpStorm\Immutable;

#[immutable]
final class BreadcrumbItem
{
    /**
     * @param string               $text
     * @param string|null          $parent
     * @param array<string, mixed> $params
     */
    public function __construct(
        public string  $text,

        public ?string $parent = null,

        public array   $params = [],
    )
    {
    }
}
