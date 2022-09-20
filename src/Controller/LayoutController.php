<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

final class LayoutController extends AbstractController
{
    /** @noinspection PhpUnused */
    public function navbar(RequestStack $requestStack, Security $security): Response
    {
        $navbarItems = [
            ['text' => 'route.name.home', 'name' => 'app_blog_post_list', 'icon' => 'fa-solid fa-house'],
            ['text' => 'route.name.image.gallery', 'name' => 'app_image_gallery'],
        ];

        $userDropdownItems   = [];
        $userDropdownItems[] = [
            'text' => 'route.name.profile',
            'name' => 'app_profile',
            'icon' => 'fa-solid fa-user',
        ];

        if ($security->isGranted(User::R_ADMIN)) {
            $userDropdownItems[] = [
                'text' => 'route.admin',
                'name' => 'app_admin',
                'icon' => 'fa-solid fa-screwdriver-wrench',
            ];

        }

        $userDropdownItems[] = [
            'text' => 'route.name.logout',
            'name' => 'app_admin_blog_post_manage',
            'icon' => 'fa-solid fa-right-from-bracket',
        ];

        return $this->render('layout/_navbar.html.twig', [
            'navbarItems'       => $navbarItems,
            'userDropdownItems' => $userDropdownItems,
            'currentRoute'      => $requestStack->getMainRequest()?->attributes->get('_route'),
        ]);
    }

    /**
     * @param array<int, array<string, string>> $icons
     *
     * @return Response
     * @noinspection PhpUnused
     */
    public function footer(#[Autowire('%app.footer.icons%')] array $icons): Response
    {
        return $this->render('layout/_footer.html.twig', [
            'icons' => $icons,
        ]);
    }
}
