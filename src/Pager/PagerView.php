<?php declare(strict_types=1);

namespace App\Pager;

use Pagerfanta\View\Template\TemplateInterface;
use Pagerfanta\View\TwitterBootstrap5View;

class PagerView extends TwitterBootstrap5View
{
    protected function createDefaultTemplate(): TemplateInterface
    {
        return new PagerTemplate();
    }
}
