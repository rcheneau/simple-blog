<?php

namespace App\Controller;

use App\Entity\Image;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Liip\ImagineBundle\Service\FilterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Handler\DownloadHandler;

#[Route(path: '/images', name: 'app_image_')]
final class ImageController extends AbstractController
{
    #[Route(path: '/large/{id}', name: 'large')]
    public function large(Image $image, CacheManager $cacheManager, FilterManager $filterManager, DataManager $dataManager): Response
    {
        $cacheManager->store(
            $filterManager->applyFilter(
                $dataManager->find('image_large', $image->getName()),
                'image_large'
            ),
            $image->getName(),
            'image_large'
        );
        $resolvedPath = $cacheManager->resolve($image->getName(), 'image_large');

        return $this->redirect($resolvedPath);
    }

    #[Route(path: '/original/{id}', name: 'original')]
    public function original(Image $image, DownloadHandler $downloadHandler): Response
    {
        return $downloadHandler->downloadObject($image, 'file', null, $image->getOriginalName(), false);
    }
}