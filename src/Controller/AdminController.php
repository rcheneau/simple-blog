<?php

namespace App\Controller;

use App\Models\Pagination;
use App\Repository\BlogPostRepository;
use App\Service\PaginationManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
final class AdminController extends AbstractController
{
    #[Route(path: '/posts', name: 'app_admin_blog_post_manage')]
    public function blogPostManage(Request            $request,
                                   BlogPostRepository $blogPostRepository,
                                   PaginationManager  $paginationManager): Response
    {
        /** @var string $route */
        $route = $request->attributes->get('_route');

        $pagination = $paginationManager->generate(
            queryBuilder:        $blogPostRepository->createBlogPostWithAuthorQueryBuilder(),
            routeName:           $route,
            page:                $request->query->getInt('page', 1),
            itemsPerPage:        $request->query->getInt('itemsPerPage', PaginationManager::DEFAULT_ITEMS_PER_PAGE),
            sortField:           (string)$request->query->get('sort', 'createdAt'),
            sortOrder:           $request->query->getAlpha('direction', Pagination::SORT_DIRECTION_DESC),
            sortFieldsWhitelist: ['createdAt' => 'post.createdAt', 'author' => 'author.username'],
            routeParams:         $request->query->getIterator()->getArrayCopy(),
        );

        return $this->render(
            $request->isXmlHttpRequest() ? 'blog_post/_datatable.html.twig' : 'admin/blog_post_manage.html.twig',
            [
                'pagination' => $pagination,
            ]
        );
    }
}
