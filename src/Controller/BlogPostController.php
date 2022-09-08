<?php

namespace App\Controller;

use App\Entity\BlogPost;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogPostController extends AbstractController
{
    #[Route(path: '/blog/{page<\d+>}', name: 'app_blog_post_list')]
    public function blogPostList(EntityManagerInterface $em, PaginatorInterface $paginator, int $page = 1): Response
    {
        $pagination = $paginator->paginate(
            $em->createQuery('SELECT p FROM App\Entity\BlogPost p ORDER BY p.createdAt DESC'),
            $page,
            12
        );

        return $this->render(
            'blog_post/list.html.twig',
            [
                'pagination' => $pagination,
            ]
        );
    }

    #[Route(path: '/blog/{slug}', name: 'app_blog_post_item')]
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
