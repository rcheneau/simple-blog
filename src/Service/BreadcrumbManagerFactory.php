<?php

declare(strict_types=1);

namespace App\Service;

use App\Models\BreadcrumbItem;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Yaml\Yaml;

final class BreadcrumbManagerFactory
{
    public function __invoke(#[Autowire('%kernel.project_dir%')] string $projectDir): BreadcrumbManager
    {
        $value = Yaml::parseFile("$projectDir/config/app_breadcrumb.yaml");

        $items = [];
        foreach ($value['app.breadcrumb'] as $k => $v) {
            $items[$k] = new BreadcrumbItem(
                $v['text'],
                $v['parent'] ?? null,
                $v['params'] ?? []
            );
        }

        return  new BreadcrumbManager($items);
    }
}
