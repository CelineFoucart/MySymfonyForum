<?php

namespace App\Controller\Post;

use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PostController extends AbstractPostController
{
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
}
