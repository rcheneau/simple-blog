<?php

declare(strict_types=1);

namespace App\Models;

use JetBrains\PhpStorm\Immutable;

#[immutable]
final class BreadcrumbItem
{
    public function __construct(
        public string $text,

        public ?string $parent = null,

        public array $params = [],
    )
    {
    }
}
