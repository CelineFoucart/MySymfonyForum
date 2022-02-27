<?php

namespace App\Controller\Topic;

use App\Entity\Post;
use App\Entity\Topic;
use App\Form\PostType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ReplyTopicController extends AbstractTopicController
{
    #[Route('/topic/{id}/reply', name: 'topic_reply')]
    #[IsGranted('ROLE_USER')]
    public function reply(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $topic = $this->getTopic($id);
        $this->denyAccessUnlessGranted('reply', $topic, 'Vous ne pouvez pas répondre à ce sujet.');
        $post = $this->getPostForReply($request, $topic);
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post->setAuthor($this->getUser())->setTopic($topic)->setCreated(new DateTime());
            $em->persist($post);
            $em->flush();
            $url = $this->generateUrl('topic', ['id' => $topic->getId(), 'slug' => $topic->getSlug()]);
            $url .= '#post'.$post->getId();

            return $this->redirect($url);
        }

        return $this->render('topic/reply.html.twig', [
            'topic' => $topic,
            'form' => $form->createView(),
        ]);
    }

    private function getPostForReply(Request $request, Topic $topic): Post
    {
        $quote = $request->query->getInt('quote');
        $post = $this->postRepository->findOneById($quote);
        if (null !== $post) {
            $author = (null !== $post->getAuthor()) ? $post->getAuthor()->getUsername() : 'Anonyme';
            $content = "[quote={$author}]{$post->getContent()}[/quote]";
        } else {
            $content = '';
        }

        return (new Post())->setTitle("Re: {$topic->getTitle()}")->setContent($content);
    }
}
