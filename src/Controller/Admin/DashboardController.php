<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Forum;
use App\Entity\Report;
use App\Entity\Role;
use App\Entity\Topic;
use App\Entity\User;
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
            ->setTitle('Administration');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToUrl('Accueil du forum', 'fa fa-globe', '/');
        yield MenuItem::linkToLogout('Déconnexion', 'fa fa-sign-out');
        yield MenuItem::section('Forum et membres', 'fas fa-list');
        yield MenuItem::linkToCrud('Catégories', 'fa fa-tags', Category::class);
        yield MenuItem::linkToCrud('Forums', 'fa fa-comments', Forum::class);
        yield MenuItem::linkToCrud('Membres', 'fa fa-users', User::class);
        yield MenuItem::linkToCrud('Rôles', 'fa fa-layer-group', Role::class);
        yield MenuItem::section("Sujets et messages", "fas fa-list");
        yield MenuItem::linkToCrud("Sujets", 'fa fa-comment', Topic::class);
        yield MenuItem::linkToCrud("Rapports", 'fas fa-exclamation', Report::class);
    }
}
