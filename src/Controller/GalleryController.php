<?php

namespace App\Controller;

use App\Entity\Image;
use App\Models\Pagination;
use App\Repository\ImageRepository;
use App\Service\PaginationManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Handler\DownloadHandler;

final class GalleryController extends AbstractController
{
    #[Route(path: '/gallery', name: 'app_gallery')]
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
            sortField:    'image.createdAt',
            sortOrder:    Pagination::SORT_DIRECTION_DESC,
        );

        return $this->render(
            $request->isXmlHttpRequest() ? 'gallery/_gallery.html.twig' : 'gallery/gallery.html.twig',
            [
                'pagination' => $pagination,
                'ajaxMode' => true,
            ]
        );
    }
}