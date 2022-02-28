<?php

namespace App\Controller\Post;

use App\Entity\Report;
use App\Form\ReportType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *  Controller used to manage the post reports.
 * 
 * @author Céline Foucart <celinefoucart@yahoo.fr>
 */
final class ReportPostController extends AbstractPostController
{
    #[Route('/post/{id}/report', name: 'post_report')]
    #[IsGranted('ROLE_USER')]
    public function report(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $post = $this->getPost($id);
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
}
