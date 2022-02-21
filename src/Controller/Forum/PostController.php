<?php

namespace App\Controller\Forum;

use App\Entity\Post;
use App\Entity\Report;
use App\Form\PostType;
use App\Form\ReportType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    #[Route('/post/{id}/edit', name: 'post_edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $post = $this->getPost($id);
        $this->denyAccessUnlessGranted('edit', $post, 'Vous ne pouvez pas éditer ce message.');
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $url = $this->generateUrl('topic', ['id' => $post->getTopic()->getId(), 'slug' => $post->getTopic()->getSlug()]);
            $url .= '#post'.$post->getId();

            return $this->redirect($url);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/post/{id}/delete', name: 'post_delete')]
    #[IsGranted('ROLE_USER')]
    public function delete(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $post = $this->getPost($id);
        $this->denyAccessUnlessGranted('delete', $post, 'Vous ne pouvez pas supprimer ce message.');
        if ($request->isMethod('POST') && $this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            foreach ($post->getReports() as $report) {
                $em->remove($report);
            }
            $em->remove($post);
            $this->addFlash('success', 'Le message a bien été supprimé.');
            $em->flush();

            return $this->redirectToRoute(
                'topic',
                ['id' => $post->getTopic()->getId(), 'slug' => $post->getTopic()->getSlug()]
            );
        }

        return $this->render('post/delete.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/post/{id}/report', name: 'post_report')]
    #[IsGranted('ROLE_USER')]
    public function report(Post $post, Request $request, EntityManagerInterface $em): Response
    {
        $report = new Report();
        $form = $this->createForm(ReportType::class, $report);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $report->setPost($post)->setAuthor($this->getUser())->setType('post');
            $em->persist($report);
            $em->flush();
            $this->addFlash('success', 'Votre rapport a bien été envoyé.');
            $topic = $post->getTopic();

            return $this->redirectToRoute('topic', ['id' => $topic->getId(), 'slug' => $topic->getSlug()]);
        }

        return $this->render('post/post_report.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
        ]);
    }

    private function getPost(int $id): Post
    {
        $post = $this->postRepository->findOneById($id);
        if (null === $post) {
            throw $this->createNotFoundException('Ce message est introuvable');
        }
        $category = $post->getTopic()->getForum()->getCategory();
        $this->denyAccessUnlessGranted('view', $category, 'Vous ne pouvez pas consulter ce forum');

        return $post;
    }
}
