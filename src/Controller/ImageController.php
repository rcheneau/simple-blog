<?php

namespace App\Controller;

use App\Entity\Image;
use App\Repository\ImageRepository;
use App\Service\PaginationManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Handler\DownloadHandler;

#[Route(path: '/images', name: 'app_image_')]
final class ImageController extends AbstractController
{
    #[Route(path: '/', name: 'gallery')]
    public function gallery(ImageRepository   $imageRepository,
                            PaginationManager $paginationManager,
                            Request           $request): Response
    {
        /** @var string $route */
        $route = $request->attributes->get('_route');

        $pagination = $paginationManager->generate(
            queryBuilder: $imageRepository->createImageQueryBuilder(),
            routeName: $route,
            page: $request->query->getInt('page', 1),
            itemsPerPage: 12,
        );

        return $this->render(
            $request->isXmlHttpRequest() ? 'image/_gallery.html.twig' : 'image/gallery.html.twig',
            [
                'pagination' => $pagination,
                'ajaxMode' => true,
            ]
        );
    }
}