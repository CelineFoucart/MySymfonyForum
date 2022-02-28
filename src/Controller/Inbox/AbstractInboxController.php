<?php

namespace App\Controller\Inbox;

use App\Entity\PrivateMessage;
use App\Entity\User;
use App\Repository\PrivateMessageRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 *  This controller provides userful method for inbox controllers.
 * 
 * @author Céline Foucart <celinefoucart@yahoo.fr>
 */
abstract class AbstractInboxController extends AbstractController
{
    protected PrivateMessageRepository $repo;
    protected UserRepository $userRepository;

    public function __construct(PrivateMessageRepository $repo, UserRepository $userRepository)
    {
        $this->repo = $repo;
        $this->userRepository = $userRepository;
    }

    protected function findPrivateMessage(int $id): PrivateMessage
    {
        $privateMessage = $this->repo->findOneById($id);
        if (null === $privateMessage) {
            throw $this->createNotFoundException('Ce message n\'existe pas');
        }
        $users = [$privateMessage->getAuthor()->getId(), $privateMessage->getAddressee()->getId()];
        $user = $this->getUser();
        assert($user instanceof User);
        if (!in_array($user->getId(), $users)) {
            throw $this->createNotFoundException('Ce message n\'existe pas');
        }

        return $privateMessage;
    }
}
