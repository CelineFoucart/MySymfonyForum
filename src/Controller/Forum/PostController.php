<?php

namespace App\Controller\Forum;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostModeratedType;
use App\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class PostController extends AbstractController
{
    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    #[Route('/post/{id}/view', name: 'post_view')]
    public function show(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $post = $this->getPost($id);
        $this->denyAccessUnlessGranted('info', $post, "Vous ne pouvez pas consulter cette page.");
        $form = $this->createForm(PostModeratedType::class, $post);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('post_view', ['id'=>$post->getId()]);
        }
        return $this->render('post/show.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

    #[Route('/post/{id}/edit', name: 'post_edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $post = $this->getPost($id);
        $this->denyAccessUnlessGranted('edit', $post, "Vous ne pouvez pas éditer ce message.");

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $url = $this->generateUrl('topic', ['id' => $post->getTopic()->getId(), 'slug' => $post->getTopic()->getSlug()]);
            $url .= '#post' . $post->getId();
            return  $this->redirect($url);
        }    

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

    #[Route('/post/{id}/delete', name: 'post_delete')]
    #[IsGranted('ROLE_USER')]
    public function delete(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $post = $this->getPost($id);
        $this->denyAccessUnlessGranted('delete', $post, "Vous ne pouvez pas supprimer ce message.");
        if($request->isMethod('POST') && $this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $em->remove($post);
            $this->addFlash('success', "Le message a bien été supprimé.");
            $em->flush();
            return $this->redirectToRoute(
                'topic', 
                ['id' => $post->getTopic()->getId(), 'slug' => $post->getTopic()->getSlug()]
            );
        }

        return $this->render('post/delete.html.twig', [
            'post' => $post
        ]);
    }

    private function getPost(int $id): Post
    {
        $post = $this->postRepository->findOneById($id);
        if($post === null) {
            throw $this->createNotFoundException("Ce message est introuvable");
        }
        return $post;
    }
}
