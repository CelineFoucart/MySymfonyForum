<?php

namespace App\Controller\Inbox;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *  Controller used to manage the inbox private message list pages.
 * 
 * @author Céline Foucart <celinefoucart@yahoo.fr>
 */
final class InboxController extends AbstractInboxController
{
    #[Route('/inbox', name: 'inbox')]
    public function index(): Response
    {
        $user = $this->getUser();
        assert($user instanceof User);
        $privateMessages = $user->getReceivedPrivateMessages();

        return $this->render('user/inbox/index.html.twig', [
            'privateMessages' => $privateMessages,
        ]);
    }

    #[Route('/inbox/sent', name: 'inbox_sent')]
    public function sent(): Response
    {
        $user = $this->getUser();
        assert($user instanceof User);
        $privateMessages = $user->getPrivateMessages();

        return $this->render('user/inbox/index.html.twig', [
            'privateMessages' => $privateMessages,
            'label' => 'Messages envoyés',
        ]);
    }

    #[Route('/inbox/{id}', name: 'inbox_show')]
    public function show(int $id): Response
    {
        $privateMessage = $this->findPrivateMessage($id);

        return $this->render('user/inbox/show.html.twig', [
            'privateMessage' => $privateMessage,
        ]);
    }
}
