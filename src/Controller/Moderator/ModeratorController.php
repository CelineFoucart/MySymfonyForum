<?php

namespace App\Controller\Moderator;

use App\Repository\ReportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/mcp')]
class ModeratorController extends AbstractController
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
        return $this->render('moderator/index.html.twig', [
            'postReports' => $postReports,
        ]);
    }

    #[Route('/report/{type}', name: 'mcp_report', requirements:['type' => 'post|mp'])]
    public function report(string $type): Response
    {
        $reports = $this->reportRepository->findPostReports($type);

        return $this->render('moderator/report.html.twig', [
            'type' => $type,
            'reports' => $reports
        ]);
    }

    #[Route('/report/{type}/{id}', name: 'mcp_report_show', requirements:['type' => 'post|mp'])]
    public function showReport(int $id, string $type, Request $request, EntityManagerInterface $em): Response
    {
        $report = $this->reportRepository->findReportById($id);
        if($report === null) {
            throw $this->createNotFoundException("Ce rapport n'existe pas");
        }
        if($request->isMethod('POST') && $this->isCsrfTokenValid('delete'.$report->getId(), $request->request->get('_token'))) {
            $em->remove($report);
            $em->flush();
            $this->addFlash('success', "Le rapport a bien été supprimé.");
            return $this->redirectToRoute('mcp_report', ['type'=>$type]);
        }

        return $this->render('moderator/report_show.html.twig', [
            'report' => $report,
            'type' => $type
        ]);
    }
    
}
