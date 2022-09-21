<?php

namespace App\Controller;

use App\DataTransformer\BlogPostInputDataTransformer;
use App\DataTransformer\ImageInputDataTransformer;
use App\Entity\BlogPost;
use App\Entity\User;
use App\Form\BlogPostType;
use App\Form\ImageType;
use App\Models\Filter;
use App\Models\Input\BlogPostInput;
use App\Models\Input\ImageInput;
use App\Models\Pagination;
use App\Repository\BlogPostRepository;
use App\Repository\ImageRepository;
use App\Service\PaginationManager;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\UuidV4;

#[Route('/admin')]
final class AdminController extends AbstractController
{
    #[Route(path: '', name: 'app_admin')]
    public function homepage(): Response
    {
        return $this->render('admin/homepage.html.twig');
    }

    #[Route(path: '/posts', name: 'app_admin_blog_post_manage')]
    public function blogPostManage(Request            $request,
                                   BlogPostRepository $blogPostRepository,
                                   PaginationManager  $paginationManager): Response
    {
        /** @var string $route */
        $route = $request->attributes->get('_route');

        $filters = [
            'title' => new Filter(
                'post.title',
                fn(QueryBuilder $qb, $v) => $qb->andWhere('post.title LIKE :title')
                    ->setParameter('title', "%$v%")
            ),
            'author' => new Filter(
                'author.username',
                fn(QueryBuilder $qb, $v) => $qb->andWhere('author.username LIKE :username')
                    ->setParameter('username', "%$v%")
            ),
        ];

        $pagination = $paginationManager->generate(
            queryBuilder: $blogPostRepository->createBlogPostWithAuthorQueryBuilder(),
            routeName: $route,
            page: $request->query->getInt('page', 1),
            itemsPerPage: $request->query->getInt('itemsPerPage', PaginationManager::DEFAULT_ITEMS_PER_PAGE),
            sortField: (string)$request->query->get('sort', 'createdAt'),
            sortOrder: $request->query->getAlpha('direction', Pagination::SORT_DIRECTION_DESC),
            sortFieldsWhitelist: [
                'createdAt' => 'post.createdAt',
                'author' => 'author.username',
                'updatedAt' => 'post.updatedAt',
                'updatedBy' => 'updated_by.username',
            ],
            filters: $filters,
            filterValues: $request->query->all('search'),
            routeParams: $request->query->getIterator()->getArrayCopy(),
        );

        return $this->render(
            $request->isXmlHttpRequest() ? 'blog_post/_blog_post_datatable.html.twig' : 'admin/blog_post_manage.html.twig',
            [
                'pagination' => $pagination,
                'ajaxMode' => true,
            ]
        );
    }

    #[Route(path: '/posts/create', name: 'app_admin_blog_post_create')]
    public function blogPostCreate(Request                      $request,
                                   BlogPostInputDataTransformer $dataTransformer,
                                   EntityManagerInterface       $em): Response
    {
        return $this->handleBlogPostForm($request, $dataTransformer, $em);
    }

    #[Route(path: '/posts/edit/{slug}', name: 'app_admin_blog_post_edit')]
    public function blogPostEdit(BlogPost                     $blogPost,
                                 Request                      $request,
                                 BlogPostInputDataTransformer $dataTransformer,
                                 EntityManagerInterface       $em): Response
    {
        return $this->handleBlogPostForm($request, $dataTransformer, $em, $blogPost);
    }

    private function handleBlogPostForm(Request                      $request,
                                        BlogPostInputDataTransformer $dataTransformer,
                                        EntityManagerInterface       $em,
                                        ?BlogPost                    $blogPost = null): Response
    {
        $form = $this->createForm(BlogPostType::class, $blogPost ? $dataTransformer->transforms($blogPost) : null);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var BlogPostInput $data */
            $data = $form->getData();

            if ($blogPost) {
                /** @var User $user */
                $user = $this->getUser();
                $data->updateBlogPost($blogPost);
                $blogPost->updatedByAt($user, new DateTimeImmutable());
            } else {
                $blogPost = $dataTransformer->createBlogPost($data);
            }

            $em->persist($blogPost);
            $em->flush();

            return $this->redirectToRoute('app_admin_blog_post_manage');
        }
        return $this->render(
            'admin/blog_post_create_edit.html.twig',
            ['form' => $form->createView(), 'blogPost' => $blogPost]
        );
    }

    #[Route(path: '/images', name: 'app_admin_image_manage')]
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

    #[Route(path: '/images/create', name: 'app_admin_image_create')]
    public function imageCreate(Request                   $request,
                                EntityManagerInterface    $em,
                                ImageInputDataTransformer $inputDataTransformer): Response
    {
        $form = $this->createForm(ImageType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ImageInput $data */
            $data = $form->getData();

            $image = $inputDataTransformer->createImage($data);

            $em->persist($image);
            $em->flush();

            return $this->redirectToRoute('app_admin_image_manage');
        }

        return $this->render(
            'admin/image_create_edit.html.twig', [
                'form' => $form->createView(),
                'image' => null,
            ]
        );
    }
}
