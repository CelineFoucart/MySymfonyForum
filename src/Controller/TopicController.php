<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Topic;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Repository\TopicRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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
            'replyPath' => $replyPath
        ]);
    }

    #[Route('/topic/{id}/reply', name: 'topic_reply')]
    #[IsGranted('ROLE_USER')]
    public function reply(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $topic = $this->getTopic($id);
        $post = $this->getPostForReply($request, $topic);

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $post->setAuthor($this->getUser())->setTopic($topic)->setCreated(new DateTime());
            $em->persist($post);
            $em->flush();
            $url = $this->generateUrl('topic', ['id' => $topic->getId(), 'slug' => $topic->getSlug()]);
            $url .= '#post' . $post->getId();
            return  $this->redirect($url);
        }
        return $this->render('topic/reply.html.twig', [
            'topic' => $topic,
            'form' => $form->createView()
        ]);
    }
    
    private function getPostForReply(Request $request, Topic $topic): Post
    {
        $quote = $request->query->getInt('quote');
        $post = $this->postRepository->findOneById($quote);
        if($post !== null) {
            $author = ($post->getAuthor() !== null) ? $post->getAuthor()->getUsername() : "Anonyme";
            $content = "[quote={$author}]{$post->getContent()}[/quote]";
        } else {
            $content = '';
        }
        return (new Post())->setTitle("Re: {$topic->getTitle()}")->setContent($content);
    }

    private function getTopic(int $id): Topic
    {
        $topic = $this->topicRepository->findOneById($id);
        if($topic === null) {
            throw $this->createNotFoundException("Ce topic n'existe pas !");
        }
        return $topic;
    }
}
