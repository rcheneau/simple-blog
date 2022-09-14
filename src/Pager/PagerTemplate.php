<?php

namespace App\Pager;

use Pagerfanta\Exception\InvalidArgumentException;
use Pagerfanta\View\Template\TwitterBootstrap5Template;

class PagerTemplate extends TwitterBootstrap5Template
{
    public function container(): string
    {
        $containerTemplate = parent::container();

        try {
            $ajaxMode = $this->option('ajax_mode');
        } catch (InvalidArgumentException) {
            $ajaxMode = false;
        }

        return $ajaxMode ? '<div data-controller="dt-paginate">' . $containerTemplate . '</div>' : $containerTemplate;
    }

    /**
     * @param int|string $text
     */
    protected function linkLi(string $class, string $href, $text, ?string $rel = null): string
    {
        $liClass = implode(' ', array_filter(['page-item', $class]));
        $rel = $rel ? sprintf(' rel="%s"', $rel) : '';

        /** @noinspection HtmlUnknownAttribute */
        /** @noinspection HtmlUnknownTarget */
        return sprintf(
            '<li class="%s"><a class="page-link" href="%s" %s data-action="dt-paginate#goTo">%s</a></li>',
            $liClass,
            $href,
            $rel,
            $text
        );
    }
}
