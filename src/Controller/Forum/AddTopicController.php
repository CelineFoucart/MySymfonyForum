<?php

namespace App\Controller\Forum;

use App\Entity\Forum;
use App\Entity\Post;
use App\Entity\Topic;
use App\Form\TopicType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 *  Controller used to manage the creation topic page.
 * 
 * @author Céline Foucart <celinefoucart@yahoo.fr>
 */
final class AddTopicController extends AbstractController
{
    #[Route('/forum/{id}/new', name: 'topic_new')]
    #[IsGranted('ROLE_USER')]
    public function add(Forum $forum, Request $request, SluggerInterface $slugger, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('view', $forum->getCategory());
        $topic = new Topic();
        $form = $this->createForm(TopicType::class, $topic);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugger->slug(strtolower($topic->getTitle()));
            $topic->setAuthor($this->getUser())->setCreated(new DateTime())->setSlug($slug)->setForum($forum);
            $em->persist($topic);

            $post = (new Post())
                ->setTitle('Re: '.$topic->getTitle())
                ->setAuthor($this->getUser())
                ->setCreated(new DateTime())
                ->setTopic($topic)
                ->setContent($form->get('message')->getData());

            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('topic', ['id' => $topic->getId(), 'slug' => $topic->getSlug()]);
        }

        return $this->render('topic/new.html.twig', [
            'forum' => $forum,
            'form' => $form->createView(),
        ]);
    }
}
