<?php

declare(strict_types=1);

namespace App\Models;

use Closure;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
final class Filter
{
    public function __construct(
        public string $field,

        public Closure $callback
    )
    {
    }
}
