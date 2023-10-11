<?php

namespace App\Controller\Admin;

use App\Entity\Blog;
use App\Entity\Category;
use App\Entity\Comments;
use App\Entity\Seller;
use App\Entity\Tags;
use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Projt Symfony Webapp');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Categories', 'fas fa-list', Category::class);
        yield MenuItem::linkToCrud('Vendeurs', 'fas fa-user', Seller::class);
        yield MenuItem::linkToCrud('Blog', 'fas fa-pen-nib', Blog::class);
        yield MenuItem::linkToCrud('Tags', 'fas fa-grip', Tags::class);
        yield MenuItem::linkToCrud('Comments', 'fas fa-comment', Comments::class);
        yield MenuItem::linkToCrud('Product', 'fas fa-shopping-cart', Product::class); 
    }
}
