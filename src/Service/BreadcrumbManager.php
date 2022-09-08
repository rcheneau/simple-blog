<?php

declare(strict_types=1);

namespace App\Service;

use App\Models\BreadcrumbItem;

final class BreadcrumbManager
{
    /**
     * @var array<string, BreadcrumbItem>
     */
    private array $bc;

    public function __construct(array $bc)
    {
        $this->bc = $bc;
    }

    /**
     * Returns array containing only items in the given node name path.
     *
     * @param string $name
     *
     * @return array<int, array<string, string>>
     */
    public function list(string $name): array
    {
        $item = $this->bc[$name] ?? null;

        if (!$item) {
            return [];
        }

        if ($item->parent) {
            return [...$this->list($item->parent), ['name' => $name, 'text' => $item->text, 'params' => $item->params]];
        }

        return [['name' => $name, 'text' => $item->text, 'params' => $item->params]];
    }
}
