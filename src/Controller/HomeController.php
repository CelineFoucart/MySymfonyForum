<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\User;
use App\Form\User\ContactType;
use App\Repository\CategoryRepository;
use App\Security\Voter\CategoryVoter;
use App\Service\EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller used to manage homepage and contact page.
 * 
 * @author Céline Foucart <celinefoucart@yahoo.fr>
 */
final class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = CategoryVoter::filterIndexCategories($categoryRepository->findByOrder(), $this->getUser());

        return $this->render('home/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, EmailService $emailService): Response
    {
        $contact = new Contact();
        $user = $this->getUser();
        if (null !== $user) {
            assert($user instanceof User);
            $contact->setUsername($user->getUsername());
            $contact->setEmail($user->getEmail());
        }

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $status = $emailService->notify($contact);
            if ($status) {
                $this->addFlash('success', 'Votre message a été envoyé');
            } else {
                $this->addFlash('error', "L'envoi a échoué");
            }

            return $this->redirectToRoute('contact');
        }

        return $this->render('home/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
