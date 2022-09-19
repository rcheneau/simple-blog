<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Models\Pagination;
use App\Repository\BlogPostRepository;
use App\Service\PaginationManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/blog')]
final class BlogPostController extends AbstractController
{
    #[Route(path: '/{page<\d+>}', name: 'app_blog_post_list')]
    public function blogPostList(BlogPostRepository $blogPostRepository,
                                 Request            $request,
                                 PaginationManager  $paginationManager,
                                 int                $page = 1): Response
    {
        /** @var string $route */
        $route = $request->attributes->get('_route');

        $pagination = $paginationManager->generate(
            queryBuilder: $blogPostRepository->createBlogPostWithAuthorQueryBuilder(),
            routeName:    $route,
            page:         $page,
            itemsPerPage: 12,
            sortField:    'post.createdAt',
            sortOrder:    Pagination::SORT_DIRECTION_DESC,
            routeParams:  $request->query->getIterator()->getArrayCopy(),
        );

        return $this->render(
            $request->isXmlHttpRequest() ? 'blog_post/_list.html.twig' : 'blog_post/list.html.twig',
            [
                'pagination' => $pagination,
                'ajaxMode' => true,
            ]
        );
    }

    #[Route(path: '/{slug}', name: 'app_blog_post_item')]
    public function blogPost(BlogPost $blogPost): Response
    {
        return $this->render(
            'blog_post/item.html.twig',
            [
                'blogPost' => $blogPost,
            ]
        );
    }
}
