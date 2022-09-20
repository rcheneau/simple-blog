<?php

namespace App\DataFixture;

use App\Entity\Image;
use Faker\Factory;
use Fidry\AliceDataFixtures\ProcessorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageProcessor implements ProcessorInterface
{
    private const PATH = __DIR__.'/assets/';
    private array $availableImageNames;

    public function __construct()
    {
        $this->availableImageNames = array_diff(scandir(self::PATH), ['.', '..']);
    }

    public function preProcess(string $id, object $object): void
    {
        if (false === $object instanceof Image) {
            return;
        }

        $faker = Factory::create();

        $imageName = $faker->randomElement($this->availableImageNames);
        $file = new UploadedFile(
            self::PATH . $imageName,
            $imageName
        );

        $object->setFile($file);
    }

    public function postProcess(string $id, object $object): void
    {

    }
}