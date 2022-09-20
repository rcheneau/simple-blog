<?php

namespace App\DataFixture;

use App\Entity\Image;
use Faker\Factory;
use Fidry\AliceDataFixtures\ProcessorInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageProcessor implements ProcessorInterface
{
    private const PATH = __DIR__ . '/assets/';
    /** @var string[] */
    private array $availableImageNames;

    public function __construct()
    {
        $filenames = scandir(self::PATH);
        if (!$filenames) {
            throw new RuntimeException(sprintf('Could load asset images from path %s.', self::PATH));
        }

        $this->availableImageNames = array_diff($filenames, ['.', '..']);
    }

    public function preProcess(string $id, object $object): void
    {
        if (false === $object instanceof Image) {
            return;
        }

        $faker = Factory::create();

        /** @var string $imageName */
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