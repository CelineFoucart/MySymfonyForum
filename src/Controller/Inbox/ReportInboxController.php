<?php

namespace App\Controller\Inbox;

use App\Entity\Report;
use App\Form\ReportType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *  Controller used to manage the inbox report page.
 * 
 * @author Céline Foucart <celinefoucart@yahoo.fr>
 */
final class ReportInboxController extends AbstractInboxController
{
    #[Route('/inbox/{id}/report', name: 'inbox_report')]
    public function report(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $privateMessage = $this->findPrivateMessage($id);
        $report = new Report();
        $form = $this->createForm(ReportType::class, $report);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $report->setPrivateMessage($privateMessage)->setAuthor($this->getUser())->setType('pm');
            $em->persist($report);
            $em->flush();
            $this->addFlash('success', 'Votre rapport a bien été envoyé.');

            return $this->redirectToRoute('inbox_show', ['id' => $privateMessage->getId()]);
        }

        return $this->render('user/inbox/inbox_report.html.twig', [
            'privateMessage' => $privateMessage,
            'form' => $form->createView(),
        ]);
    }
}
