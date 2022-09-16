<?php

namespace App\DataFixture;

use App\Entity\BlogPost;
use App\Repository\UserRepository;
use DateInterval;
use DateTimeImmutable;
use Faker\Factory;
use Fidry\AliceDataFixtures\ProcessorInterface;

/**
 * Set updated values for some posts.
 */
class BlogPostProcessor implements ProcessorInterface
{
    public function preProcess(string $id, object $object): void
    {
        if (false === $object instanceof BlogPost) {
            return;
        }

        $faker = Factory::create();

        if($faker->boolean(25)) {
            $object->updatedByAt(
                $object->getAuthor(),
                DateTimeImmutable::createFromMutable(
                    $faker->dateTimeBetween(
                        $object->getCreatedAt()->format('Y-m-d H:i:s'),
                        $object->getCreatedAt()->add(new DateInterval('P10D'))->format('Y-m-d H:i:s')
                    )
                )
            );
        }
    }

    public function postProcess(string $id, object $object): void
    {

    }
}