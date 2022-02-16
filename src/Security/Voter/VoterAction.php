<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;

class VoterAction
{
    private Security $security;

    const EDIT = "edit";
    const DELETE = "delete";
    const INFORMATIONS = "info";

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Determine if a user can moderate a post or a topic
     * 
     * @return bool
     */
    public function canModerate(): bool
    {
        return $this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_MODERATOR');
    }

    /**
     * Determine if a user can edit a post or a topic 
     * 
     * @param Post|Topic $item
     * @param User $user
     * 
     * @return bool
     */
    public function canEdit($item, User $user): bool
    {
        $author = $item->getAuthor();
        if($author === null) {
            return false;
        } else {
            return $user->getId() === $author->getId();
        }
    }

    /**
     * Determine if a user can delete a post or a topic 
     * 
     * @param Post|Topic $item
     * @param User $user
     * 
     * @return bool
     */
    public function canDelete($item, User $user): bool
    {
        return $this->canEdit($item, $user);
    }
}