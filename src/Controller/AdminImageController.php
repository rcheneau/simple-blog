<?php

namespace App\Controller;

use App\DataTransformer\ImageInputDataTransformer;
use App\Entity\Image;
use App\Entity\User;
use App\Form\ImageType;
use App\Models\Filter;
use App\Models\Input\ImageInput;
use App\Models\Pagination;
use App\Repository\ImageRepository;
use App\Service\PaginationManager;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use http\Url;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Handler\UploadHandler;

#[Route('/admin', name: 'app_admin_image_')]
final class AdminImageController extends AbstractController
{
    #[Route(path: '/images', name: 'manage')]
    public function imageManage(ImageRepository   $imageRepository,
                                PaginationManager $paginationManager,
                                Request           $request): Response
    {
        /** @var string $route */
        $route = $request->attributes->get('_route');

        $filters = [
            'title' => new Filter(
                'image.title',
                fn(QueryBuilder $qb, $v) => $qb->andWhere('LOWER(image.title) LIKE :title')
                    ->setParameter('title', '%' . strtolower($v) . '%')
            ),
            'createdBy' => new Filter(
                'createdBy.username',
                fn(QueryBuilder $qb, $v) => $qb->andWhere('createdBy.username LIKE :username')
                    ->setParameter('username', "%$v%")
            ),
        ];

        $pagination = $paginationManager->generate(
            queryBuilder: $imageRepository->createImageQueryBuilder(),
            routeName: $route,
            page: $request->query->getInt('page', 1),
            itemsPerPage: $request->query->getInt('itemsPerPage', 12),
            sortField: (string)$request->query->get('sort', 'createdAt'),
            sortOrder: $request->query->getAlpha('direction', Pagination::SORT_DIRECTION_DESC),
            sortFieldsWhitelist: [
                'createdAt' => 'image.createdAt',
                'createdBy' => 'createdBy.username',
            ],
            filters: $filters,
            filterValues: $request->query->all('search'),
            routeParams: $request->query->getIterator()->getArrayCopy(),
        );

        return $this->render(
            $request->isXmlHttpRequest() ? 'gallery/_gallery_datatable.html.twig' : 'admin/image_manage.html.twig',
            [
                'pagination' => $pagination,
                'ajaxMode' => true,
            ]
        );
    }

    #[Route(path: '/images/create', name: 'create')]
    public function imageCreate(Request                   $request,
                                EntityManagerInterface    $em,
                                ImageInputDataTransformer $inputDataTransformer): Response
    {
        return $this->handleImageForm($request, $inputDataTransformer, $em);
    }

    #[Route(path: '/images/edit/{id}', name: 'edit')]
    public function imageEdit(Image                     $image,
                              Request                   $request,
                              EntityManagerInterface    $em,
                              UploadHandler             $uploadHandler,
                              ImageInputDataTransformer $inputDataTransformer): Response
    {
        $uploadHandler->inject($image, 'file');

        return $this->handleImageForm($request, $inputDataTransformer, $em, $image);
    }

    #[Route(path: '/images/delete/{id}', name: 'delete', methods: Request::METHOD_POST)]
    public function deleteImage(Image $image, EntityManagerInterface $em, Request $request): Response
    {
        if (false && $this->isCsrfTokenValid('delete-image-' . $image->getId()->toRfc4122(), $request->headers->get('X-CSRF-Token'))) {
            $em->remove($image);
            $em->flush();
            return new Response(null, 204);
        }

        return new Response(null, 403);
    }

    private function handleImageForm(Request                   $request,
                                     ImageInputDataTransformer $dataTransformer,
                                     EntityManagerInterface    $em,
                                     ?Image                    $image = null): Response
    {
        $form = $this->createForm(ImageType::class, $image ? $dataTransformer->transforms($image) : null);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ImageInput $data */
            $data = $form->getData();

            if ($image) {
                /** @var User $user */
                $user = $this->getUser();
                $data->updateImage($image);
                $image->updatedByAt($user, new DateTimeImmutable());
            } else {
                $image = $dataTransformer->createImage($data);
            }

            $em->persist($image);
            $em->flush();

            return $this->redirectToRoute('app_admin_image_manage');
        }

        return $this->render(
            'admin/image_create_edit.html.twig',
            ['form' => $form->createView(), 'image' => $image]
        );
    }
}
