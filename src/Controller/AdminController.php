<?php

namespace App\Controller;

use App\DataTransformer\BlogPostInputDataTransformer;
use App\Entity\BlogPost;
use App\Form\BlogPostType;
use App\Models\Filter;
use App\Models\Input\BlogPostInput;
use App\Models\Pagination;
use App\Repository\BlogPostRepository;
use App\Service\PaginationManager;
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
            'title'  => new Filter(
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
            sortFieldsWhitelist: ['createdAt' => 'post.createdAt', 'author' => 'author.username'],
            filters: $filters,
            filterValues: $request->query->all('search'),
            routeParams: $request->query->getIterator()->getArrayCopy(),
        );

        return $this->render(
            $request->isXmlHttpRequest() ? 'blog_post/_datatable.html.twig' : 'admin/blog_post_manage.html.twig',
            [
                'pagination' => $pagination,
                'ajaxMode'   => true,
            ]
        );
    }

    #[Route(path: '/posts/create', name: 'app_admin_blog_post_create')]
    public function blogPostCreate(Request                      $request,
                                   BlogPostInputDataTransformer $dataTransformer,
                                   EntityManagerInterface       $em): Response
    {
        $form = $this->createForm(BlogPostType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var BlogPostInput $data */
            $data = $form->getData();
            $post = $dataTransformer->transforms($data);

            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('app_admin_blog_post_manage');
        }

        return $this->render('admin/blog_post_create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
