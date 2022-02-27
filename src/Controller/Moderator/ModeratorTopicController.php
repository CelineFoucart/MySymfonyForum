<?php

namespace App\Controller\Moderator;

use App\Entity\Topic;
use App\Form\TopicMoveType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/mcp/topic')]
final class ModeratorTopicController extends AbstractController
{
    #[Route('/{id}/delete', name: 'topic_delete')]
    public function delete(Topic $topic, Request $request, EntityManagerInterface $em): Response
    {
        $forum = $topic->getForum();
        if ($request->isMethod('POST') && $this->isCsrfTokenValid('delete'.$topic->getId(), $request->request->get('_token'))) {
            $em->remove($topic);
            $this->addFlash('success', 'Le sujet a bien été supprimé.');
            $em->flush();

            return $this->redirectToRoute('forum', ['id' => $forum->getId(), 'slug' => $forum->getSlug()]);
        }

        return $this->render('moderator/topic/delete.html.twig', [
            'topic' => $topic,
        ]);
    }

    #[Route('/{id}/move', name: 'topic_move')]
    public function move(Topic $topic, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TopicMoveType::class, $topic);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('topic', ['id' => $topic->getId(), 'slug' => $topic->getSlug()]);
        }

        return $this->render('moderator/topic/move.html.twig', [
            'topic' => $topic,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/lock', name: 'topic_lock')]
    public function lock(Topic $topic, Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST') && $this->isCsrfTokenValid('lock'.$topic->getId(), $request->request->get('_token'))) {
            $topic->setLocked(!$topic->getLocked());
            $em->flush();
        }

        return $this->render('moderator/topic/lock.html.twig', [
            'topic' => $topic,
        ]);
    }
}
