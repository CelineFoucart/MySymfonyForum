<?php

namespace App\Controller\Moderator;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/mcp')]
class ModeratorController extends AbstractController
{
    #[Route('/', name: 'mcp_home')]
    public function index(): Response
    {
        return $this->render('moderator/index.html.twig', [
            'controller_name' => 'ModeratorController',
        ]);
    }

    #[Route('/report/{type}', name: 'mcp_report', requirements:['type' => 'post|mp'])]
    public function report(): Response
    {
        return $this->render('moderator/report.html.twig', []);
    }
}
