<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\TopicRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TopicController extends AbstractController
{
    private TopicRepository $topicRepository;
    private PostRepository $postRepository;

    public function __construct(TopicRepository $topicRepository, PostRepository $postRepository)
    {
        $this->topicRepository = $topicRepository;
        $this->postRepository = $postRepository;
    }

    #[Route('/topic/{slug}-{id}', name: 'topic', requirements:['slug' => '[a-z\-]*'])]
    public function index(int $id, string $slug, Request $request): Response
    {
        $topic = $this->topicRepository->findOneById($id);
        if($topic === null) {
            throw $this->createNotFoundException("Ce topic n'existe pas !");
        } elseif($topic->getSlug() !== $slug) {
            return $this->redirectToRoute('topic', ['id' => $topic->getId(), 'slug' => $topic->getSlug()]);
        }
        $page = $request->query->getInt('page', 1);
        $posts = $this->postRepository->findPaginated($topic->getId(), $page);

        return $this->render('topic/topic.html.twig', [
            'topic' => $topic,
            'posts' => $posts
        ]);
    }
}
