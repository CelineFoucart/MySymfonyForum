<?php

namespace App\Controller\Inbox;

use App\Entity\PrivateMessage;
use App\Form\PrivateMessageType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CreateInboxController extends AbstractInboxController
{
    #[Route('/inbox/new', name: 'inbox_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reply = $this->repo->findOneById($request->query->getInt('reply', 0));
        $replyTo = $request->query->get('replyTo', '');
        $pm = new PrivateMessage();
        $form = $this->createForm(PrivateMessageType::class, $pm);
        if (null !== $reply) {
            $form->get('addressee')->setData($reply->getAuthor()->getUsername());
            $form->get('title')->setData("Re: {$reply->getTitle()}");
        } elseif (null != $replyTo) {
            $form->get('addressee')->setData($replyTo);
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pseudo = $form->get('addressee')->getData();
            $addressee = $this->userRepository->findByPseudo($pseudo);
            if (null === $addressee) {
                $this->addFlash(
                   'error',
                   "L'utilisateur {$pseudo} n'existe pas."
                );
            } else {
                $pm->setCreated(new DateTime())->setAuthor($this->getUser())->setAddressee($addressee);
                $entityManager->persist($pm);
                $entityManager->flush();
                $this->addFlash(
                'success',
                'Le message privé a bien été envoyé.'
                );

                return $this->redirectToRoute('inbox_show', ['id' => $pm->getId()]);
            }
        }

        return $this->render('user/inbox/new.html.twig', [
            'form' => $form->createView(),
            'reply' => $reply,
        ]);
    }
}
