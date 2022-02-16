<?php

namespace App\Security\Voter;

use App\Entity\Post;
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
        /** @var Post */
        $post = $subject;
        switch ($attribute) {
            case VoterAction::EDIT:
                return $this->voterAction->canEdit($post, $user);
                break;
            case VoterAction::DELETE:
                return $this->voterAction->canDelete($post, $user);
                break;
                case VoterAction::INFORMATIONS:
                    return $this->voterAction->canModerate();
                    break;
        }

        return false;
    }
}
