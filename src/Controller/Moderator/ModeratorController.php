<?php

namespace App\Controller\Moderator;

use App\Repository\ReportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *  Controller used to manage the moderator control pannel home page.
 * 
 * @author Céline Foucart <celinefoucart@yahoo.fr>
 */
#[Route('/mcp')]
final class ModeratorController extends AbstractController
{
    private ReportRepository $reportRepository;

    public function __construct(ReportRepository $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    #[Route('/', name: 'mcp_home')]
    public function index(): Response
    {
        $postReports = $this->reportRepository->findLastReports('post');
        $pmReports = $this->reportRepository->findLastReports('pm');

        return $this->render('moderator/index.html.twig', [
            'postReports' => $postReports,
            'pmReports' => $pmReports,
        ]);
    }
}
