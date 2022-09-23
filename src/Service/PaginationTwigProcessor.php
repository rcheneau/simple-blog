<?php

declare(strict_types=1);

namespace App\Service;

use App\Models\Pagination;
use RuntimeException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Generates parameters needed to render "datatable/_sortable_link.html.twig".
 */
final class PaginationTwigProcessor
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Create a sort url for the field named $title and identified by $key which consists of alias and field.
     * $attributes holds all link parameters like "alt, class" and so on.
     * $options allows to control query parameter's names:
     *      - param_sort_name, by default 'sort'
     *      - param_direction_name, by default 'direction'
     *      - param_page_name, by default 'page'
     *
     * @param Pagination           $pagination
     * @param string               $title
     * @param string               $key
     * @param bool                 $ajaxMode
     * @param array<string, mixed> $attributes
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    public function sortable(Pagination $pagination,
                             string     $title,
                             string     $key,
                             bool       $ajaxMode = false,
                             array      $attributes = [],
                             array      $options = []): array
    {
        $paramSortName      = $options['param_sort_name'] ?? 'sort';
        $paramDirectionName = $options['param_direction_name'] ?? 'direction';
        $paramPageName      = $options['param_page_name'] ?? 'page';

        $params = $pagination->routeParams;

        [$direction, $oppositeDirection] = Pagination::SORT_DIRECTION_DESC === $pagination->sortOrder
            ? [Pagination::SORT_DIRECTION_DESC, Pagination::SORT_DIRECTION_ASC]
            : [Pagination::SORT_DIRECTION_ASC, Pagination::SORT_DIRECTION_DESC];

        $params[$paramSortName]      = $key;
        $params[$paramDirectionName] = $oppositeDirection;
        $params[$paramPageName]      = 1;

        $attributes['href'] = $this->urlGenerator->generate($pagination->routeName, $params);

        return [
            'options'    => $options,
            'attributes' => $attributes,
            'title'      => $title,
            'ajaxMode'   => $ajaxMode,
            'sorted'     => $pagination->sortField === $key,
            'direction'  => $direction,
        ];
    }

    /**
     * Create a search url for the field named $title and identified by $key which consists of alias and field.
     * $options allows to control query parameter's names:
     *      - param_page_name, by default 'page'
     *
     * @param Pagination           $pagination
     * @param string               $title
     * @param string               $key
     * @param string               $placeholder
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    public function filterable(Pagination $pagination,
                               string     $title,
                               string     $key,
                               string     $placeholder = '',
                               array      $options = []): array
    {
        $paramPageName = $options['param_page_name'] ?? 'page';

        $params                 = $pagination->routeParams;
        $params[$paramPageName] = 1;

        return [
            'title'       => $title,
            'key'         => $key,
            'value'       => $pagination->filters[$key] ?? '',
            'placeholder' => $placeholder,
            'routeName'   => $pagination->routeName,
            'routeParams' => $this->urlGenerator->generate(
                $pagination->routeName,
                $params,
                UrlGeneratorInterface::RELATIVE_PATH
            ),
        ];
    }
}
