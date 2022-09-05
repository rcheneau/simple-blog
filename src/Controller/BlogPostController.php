<?php

namespace App\Controller;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class BlogPostController extends AbstractController
{
    #[Route(path: '/', name: 'app_blog_post_list')]
    public function list(): Response
    {
        return $this->render('blog_post/list.html.twig');
    }
}
