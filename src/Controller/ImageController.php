<?php

namespace App\Controller;

use App\Entity\Image;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Handler\DownloadHandler;

#[Cache(expires: '+2 days', public: true)]
#[Route(path: '/images', name: 'app_image_')]
final class ImageController extends AbstractController
{
    #[Route(path: '/large/{id}', name: 'large')]
    public function large(Image $image, CacheManager $cacheManager, FilterManager $filterManager, DataManager $dataManager, #[Autowire('%kernel.project_dir%')] string $projectDir): Response
    {
        if (!$cacheManager->isStored($image->getName(), 'image_large')) {
            $largeImage = $filterManager->applyFilter(
                $dataManager->find('image_large', $image->getName()),
                'image_large'
            );
            $cacheManager->store(
                $largeImage,
                $image->getName(),
                'image_large'
            );
        }

        $path = ltrim((string)parse_url($cacheManager->resolve($image->getName(), 'image_large'), PHP_URL_PATH), '/');

        return new BinaryFileResponse("$projectDir/public/$path");
    }

    #[Route(path: '/original/{id}', name: 'original')]
    public function original(Image $image, DownloadHandler $downloadHandler): Response
    {
        return $downloadHandler->downloadObject($image, 'file', null, $image->getOriginalName(), false);
    }
}