<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Repository\PrivateMessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InboxController extends AbstractController
{
    private PrivateMessageRepository $repo;

    public function __construct(PrivateMessageRepository $repo)
    {
        $this->repo = $repo;
    }
    #[Route('/inbox', name: 'inbox')]
    public function index(): Response
    {
        /** @var User */
        $user = $this->getUser();
        $privateMessages = $user->getReceivedPrivateMessages();

        return $this->render('user/inbox/index.html.twig', [
            'privateMessages' => $privateMessages,
        ]);
    }

    #[Route('/inbox/sent', name: 'inbox_sent')]
    public function sent(): Response
    {
        /** @var User */
        $user = $this->getUser();
        $privateMessages = $user->getPrivateMessages();

        return $this->render('user/inbox/index.html.twig', [
            'privateMessages' => $privateMessages,
            'label' => "Messages envoyés"
        ]);
    }

    #[Route('/inbox/new', name: 'inbox_create')]
    public function create(): Response
    {
        return $this->render('user/inbox/new.html.twig', []);
    }

    #[Route('/inbox/{id}', name: 'inbox_show')]
    public function show(): Response
    {
        return $this->render('user/inbox/show.html.twig', []);
    }

    #[Route('/inbox/create/{id}/reply', name: 'inbox_reply')]
    public function reply(): Response
    {
        return $this->render('user/inbox/new.html.twig', []);
    }
}
