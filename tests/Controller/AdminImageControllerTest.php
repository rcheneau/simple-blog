<?php

namespace App\Tests\Controller;

use App\Repository\BlogPostRepository;
use App\Repository\ImageRepository;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AdminImageControllerTest extends AbstractControllerTest
{
    public function testAdminImageManage()
    {
        $client = static::createClient();
        $this->login($client);
        $client->request('GET', '/fr/admin/images');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Gestion des images');
    }

    public function testCreateImage()
    {
        $client = static::createClient();

        /** @var ImageRepository $imageRepository */
        $imageRepository = static::getContainer()->get(ImageRepository::class);
        $count = $imageRepository->count([]);

        $this->login($client);

        $path = __DIR__ . '/../../src/DataFixture/assets';
        $image =  current(array_diff(scandir(__DIR__ . '/../../src/DataFixture/assets'), ['.', '..']));
        $uploadedFile = new UploadedFile(
            "$path/$image",
            $image
        );

        $crawler = $client->request('GET', '/fr/admin/images/create');
        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form')->form();
        $form['image[title]'] = 'Image title';
        $form['image[description]'] = 'My description...';
        $form['image[file][file]'] = $uploadedFile;
        $client->submit($form);

        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('title', 'Gestion des images');

        $this->assertEquals($count + 1, $imageRepository->count([]));
    }
}
