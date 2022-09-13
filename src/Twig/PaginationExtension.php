<?php

namespace App\Twig;


use App\Models\Pagination;
use App\Service\PaginationTwigProcessor;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class PaginationExtension extends AbstractExtension
{
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
        ];
    }

    /**
     * @param Environment          $env
     * @param Pagination           $pagination
     * @param string               $title
     * @param string               $key
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
                             array       $attributes = [],
                             array       $options = []): string
    {
        return $env->render(
            'pagination/_sortable_link.html.twig',
            $this->processor->sortable($pagination, $title, $key, $attributes, $options)
        );
    }
}
