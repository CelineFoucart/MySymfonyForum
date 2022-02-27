<?php

namespace App\Security\Voter;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class PostVoter extends Voter
{
    private VoterAction $voterAction;

    public function __construct(VoterAction $voterAction)
    {
        $this->voterAction = $voterAction;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [VoterAction::DELETE, VoterAction::EDIT, VoterAction::INFORMATIONS])
            && $subject instanceof \App\Entity\Post;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }
        if ($this->voterAction->canModerate()) {
            return true;
        }
        $post = $subject;
        switch ($attribute) {
            case VoterAction::EDIT:
                return $this->canEditPost($post, $user);
                break;
            case VoterAction::DELETE:
                return $this->canDeletePost($post, $user);
                break;
                case VoterAction::INFORMATIONS:
                    return $this->voterAction->canModerate();
                    break;
        }

        return false;
    }

    /**
     * Determines if a user can edit a post.
     */
    protected function canEditPost(Post $post, User $user): bool
    {
        return $this->voterAction->canEdit($post, $user) && $post->getTopic()->getLocked() !== true;
    }

    /**
     * Determines if a user can delete a post.
     */
    protected function canDeletePost(Post $post, User $user): bool
    {
        return $this->canEditPost($post, $user);
    }
}
