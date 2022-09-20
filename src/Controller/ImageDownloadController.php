<?php

namespace App\Controller;

use App\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Handler\DownloadHandler;

#[Route(path: '/images', name: 'app_image_')]
final class ImageDownloadController extends AbstractController
{
    #[Route(path: '/download/{id}', name: 'download')]
    public function download(Image $image, DownloadHandler $downloadHandler): Response
    {
        return $downloadHandler->downloadObject($image, 'file', null, $image->getOriginalName(), false);
    }
}