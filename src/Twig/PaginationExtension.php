<?php

namespace App\Twig;


use App\Models\Pagination;
use App\Service\PaginationTwigProcessor;
use RuntimeException;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class PaginationExtension extends AbstractExtension
{
    private const FILTER_TYPES
        = [
            'text' => 'datatable/_filter_text.html.twig',
        ];


    private PaginationTwigProcessor $processor;

    public function __construct(PaginationTwigProcessor $processor)
    {
        $this->processor = $processor;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'pagination_sortable',
                [$this, 'sortable'],
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
            new TwigFunction(
                'pagination_filterable',
                [$this, 'filterable'],
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
        ];
    }

    /**
     * @param Environment          $env
     * @param Pagination           $pagination
     * @param string               $title
     * @param string               $key
     * @param bool                 $ajaxMode
     * @param array<string, mixed> $attributes
     * @param array<string, mixed> $options
     *
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sortable(Environment $env,
                             Pagination  $pagination,
                             string      $title,
                             string      $key,
                             bool        $ajaxMode = false,
                             array       $attributes = [],
                             array       $options = []): string
    {
        return $env->render(
            'datatable/_sortable_link.html.twig',
            $this->processor->sortable($pagination, $title, $key, $ajaxMode, $attributes, $options)
        );
    }

    /**
     * @param Environment          $env
     * @param Pagination           $pagination
     * @param string               $title
     * @param string               $key
     * @param string               $placeholder
     * @param string               $type
     * @param array<string, mixed> $options
     *
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function filterable(Environment $env,
                               Pagination  $pagination,
                               string      $title,
                               string      $key,
                               string      $placeholder = '',
                               string      $type = 'text',
                               array       $options = []): string
    {
        $template = self::FILTER_TYPES[$type] ?? null;
        if ($template === null) {
            throw new RuntimeException(sprintf('Unknown type "%s"', $type));
        }

        return $env->render(
            $template,
            $this->processor->filterable($pagination, $title, $key, $placeholder, $options)
        );
    }
}
