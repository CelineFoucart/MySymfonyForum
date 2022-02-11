<?php

namespace App\Controller;

use App\Repository\TopicRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TopicController extends AbstractController
{
    private TopicRepository $topicRepository;

    public function __construct(TopicRepository $topicRepository)
    {
        $this->topicRepository = $topicRepository;
    }

    #[Route('/topic/{slug}-{id}', name: 'topic', requirements:['slug' => '[a-z\-]*'])]
    public function index(int $id, string $slug): Response
    {
        $topic = $this->topicRepository->find($id);
        if($topic === null) {
            throw $this->createNotFoundException("Ce topic n'existe pas !");
        } elseif($topic->getSlug() !== $slug) {
            return $this->redirectToRoute('topic', ['id' => $topic->getId(), 'slug' => $topic->getSlug()]);
        }
        return $this->render('topic/topic.html.twig', [
            'topic' => $topic,
        ]);
    }
}
