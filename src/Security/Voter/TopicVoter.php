<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TopicVoter extends Voter
{
    private VoterAction $voterAction;

    public function __construct(VoterAction $voterAction)
    {
        $this->voterAction = $voterAction;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [VoterAction::DELETE, VoterAction::EDIT, VoterAction::INFORMATIONS])
            && $subject instanceof \App\Entity\Topic;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }
        switch ($attribute) {
            case VoterAction::DELETE:
                return $this->voterAction->canModerate();
                break;
            case VoterAction::EDIT:
                return $this->voterAction->canModerate() || $this->voterAction->canEdit($subject, $user);
                break;
            case VoterAction::INFORMATIONS:
                return $this->voterAction->canModerate();
                break;
        }

        return false;
    }
}
