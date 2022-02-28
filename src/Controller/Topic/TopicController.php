<?php

namespace App\Controller\Topic;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *  Controller used to manage the topics.
 * 
 * @author Céline Foucart <celinefoucart@yahoo.fr>
 */
final class TopicController extends AbstractTopicController
{
    #[Route('/topic/{slug}-{id}', name: 'topic', requirements: ['slug' => '[a-z\-]*'])]
    public function index(int $id, string $slug, Request $request): Response
    {
        $topic = $this->getTopic($id);
        if ($topic->getSlug() !== $slug) {
            return $this->redirectToRoute('topic', ['id' => $topic->getId(), 'slug' => $topic->getSlug()]);
        }
        $page = $request->query->getInt('page', 1);
        $posts = $this->postRepository->findPaginated($topic->getId(), $page);
        $replyPath = $this->generateUrl('topic_reply', ['id' => $topic->getId()]);

        return $this->render('topic/topic.html.twig', [
            'topic' => $topic,
            'posts' => $posts,
            'replyPath' => $replyPath,
        ]);
    }
}
