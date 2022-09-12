<?php

declare(strict_types=1);

namespace App\Service;

use App\Models\BreadcrumbItem;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Yaml\Yaml;

final class BreadcrumbManagerFactory
{
    public function __invoke(#[Autowire('%kernel.project_dir%')] string $projectDir): BreadcrumbManager
    {
        $file = "$projectDir/config/app_breadcrumb.yaml";
        $value = Yaml::parseFile($file);
        if (!is_array($value)) {
            throw new InvalidConfigurationException("Missing key 'app.breadcrumb' for file $file");
        }

        $bc = $value['app.breadcrumb'] ?? [];
        if (!is_array($bc)) {
            throw new InvalidConfigurationException("Excepts array under key 'app.breadcrumb' for file $file");
        }

        $items = [];
        foreach ($bc as $k => $v) {
            $items[$k] = new BreadcrumbItem(
                $v['text'],
                $v['parent'] ?? null,
                $v['params'] ?? []
            );
        }

        return  new BreadcrumbManager($items);
    }
}
