<?php

namespace App\Tests\Controller;

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

    public function testAdminUpdateImage()
    {
        $client = static::createClient();

        /** @var ImageRepository $imageRepository */
        $imageRepository = static::getContainer()->get(ImageRepository::class);
        $image = $imageRepository->findOneBy([]);

        $loggedUser = $this->login($client);

        $crawler = $client->request('GET', '/fr/admin/images/edit/' . $image->getId()->toRfc4122());
        $this->assertResponseIsSuccessful();
        $this->assertInputValueSame('image[title]', $image->getTitle());
        $this->assertInputValueSame('image[description]', $image->getDescription());
        $this->assertEquals('Télécharger', $crawler->filter('.vich-image a[download]')->innerText());

        $form = $crawler->filter('form')->form();
        $form['image[title]'] = 'Updated title';
        $form['image[description]'] = 'Updated description';
        $client->submit($form);

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Gestion des images');

        $updatedImage = $imageRepository->find($image->getId());
        $this->assertEquals('Updated title', $updatedImage->getTitle());
        $this->assertEquals('Updated description', $updatedImage->getDescription());
        $this->assertEquals($loggedUser->getId(), $updatedImage->getUpdatedBy()->getId());
        $this->assertEquals($updatedImage->getUpdatedAt()->format('Y-m-d'), (new DateTimeImmutable())->format('Y-m-d'));
    }
}
