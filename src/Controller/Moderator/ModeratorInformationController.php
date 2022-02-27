<?php

namespace App\Controller\Moderator;

use App\Entity\Topic;
use App\Form\Moderator\PostModeratedType;
use App\Form\Moderator\TopicModeratedType;
use App\Repository\PostRepository;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/mcp/informations')]
final class ModeratorInformationController extends AbstractController
{
    private PostRepository $postRepository;
    private TopicRepository $topicRepository;

    public function __construct(PostRepository $postRepository, TopicRepository $topicRepository)
    {
        $this->postRepository = $postRepository;
        $this->topicRepository = $topicRepository;
    }

    #[Route('/', name: 'mcp_view')]
    public function show(Request $request): Response
    {
        $error = false;
        if ('POST' === $request->getMethod()) {
            $type = $request->request->get('type');
            if ($this->validateSearch($request->request->get('type'), $request)) {
                return $this->redirectToRoute($type.'_view', ['id' => $request->request->getInt('id')]);
            } else {
                $error = "L'élément {$request->request->get('id')} de type \"{$request->request->get('type')}\" n'existe pas.";
            }
        }

        return $this->render('moderator/informations/informations.html.twig', [
            'error' => $error,
        ]);
    }

    #[Route('/post/{id}', name: 'post_view')]
    public function showPost(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $post = $this->postRepository->findOneById($id);
        if (null === $post) {
            throw $this->createNotFoundException('Ce message est introuvable');
        }
        $form = $this->createForm(PostModeratedType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('post_view', ['id' => $post->getId()]);
        }

        return $this->render('moderator/informations/post.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/topic/{id}', name: 'topic_view')]
    public function showTopic(Topic $topic, Request $request, SluggerInterface $slugger, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TopicModeratedType::class, $topic);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugger->slug(strtolower($topic->getTitle()));
            $topic->setSlug($slug);
            $em->flush();
        }

        return $this->render('moderator/informations/topic.html.twig', [
            'topic' => $topic,
            'form' => $form->createView(),
        ]);
    }

    private function validateSearch(string $type, Request $request): bool
    {
        $id = $request->request->getInt('id', 0);
        switch ($type) {
            case 'topic':
                $count = $this->topicRepository->count(['id' => $id]);
                break;
            case 'post':
                $count = $this->postRepository->count(['id' => $id]);
                break;
            default:
                $count = 0;
                break;
        }

        return $count > 0;
    }
}
