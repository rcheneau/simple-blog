<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogPostController extends AbstractController
{
    #[Route(path: '/{page}', name: 'app_blog_post_list')]
    public function list(EntityManagerInterface $em, PaginatorInterface $paginator, int $page = 1): Response
    {
        $pagination = $paginator->paginate(
            $em->createQuery('SELECT p FROM App\Entity\BlogPost p'),
            $page,
            10
        );

        return $this->render(
            'blog_post/list.html.twig',
            [
                'pagination' => $pagination,
            ]
        );
    }
}
