<?php

namespace App\Controller\Inbox;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DeleteInboxController extends AbstractInboxController
{
    #[Route('/inbox/{id}/delete', name: 'inbox_delete')]
    public function delete(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $privateMessage = $this->findPrivateMessage($id);
        if ($request->isMethod('POST')
            && $this->isCsrfTokenValid('delete'.$privateMessage->getId(), $request->request->get('_token'))) {
            foreach ($privateMessage->getReports() as $report) {
                $em->remove($report);
            }
            $em->remove($privateMessage);
            $this->addFlash('success', 'Le message a bien été supprimé.');
            $em->flush();

            return $this->redirectToRoute('inbox');
        }

        return $this->render('user/inbox/delete.html.twig', [
            'privateMessage' => $privateMessage,
        ]);
    }
}
