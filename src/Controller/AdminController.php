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

#[Route('/admin', name: 'app_admin_')]
final class AdminController extends AbstractController
{
    #[Route(path: '', name: 'home')]
    public function homepage(): Response
    {
        return $this->render('admin/homepage.html.twig');
    }
}
