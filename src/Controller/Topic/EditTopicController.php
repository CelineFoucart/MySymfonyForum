<?php

namespace App\Controller\Topic;

use App\Form\TopicEditType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class EditTopicController extends AbstractTopicController
{
    #[Route('/topic/{id}/edit', name: 'topic_edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(int $id, Request $request, SluggerInterface $slugger, EntityManagerInterface $em): Response
    {
        $topic = $this->getTopic($id);
        $this->denyAccessUnlessGranted('edit', $topic);
        $form = $this->createForm(TopicEditType::class, $topic);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugger->slug(strtolower($topic->getTitle()));
            $topic->setSlug($slug);
            $em->flush();

            return $this->redirectToRoute('topic', ['id' => $topic->getId(), 'slug' => $topic->getSlug()]);
        }

        return $this->render('topic/edit.html.twig', [
            'topic' => $topic,
            'form' => $form->createView(),
        ]);
    }
}
