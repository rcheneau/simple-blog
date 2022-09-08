<?php

declare(strict_types=1);

namespace App\DataFixture;

use Fidry\AliceDataFixtures\ProcessorInterface;

final class BlogPostProcessor implements ProcessorInterface
{

    public function preProcess(string $id, object $object): void
    {
        // TODO: Implement preProcess() method.
    }

    public function postProcess(string $id, object $object): void
    {
        // TODO: Implement postProcess() method.
    }
}
