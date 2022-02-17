<?php

namespace App\Security\Voter;

use App\Entity\Topic;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TopicVoter extends Voter
{
    private VoterAction $voterAction;

    const REPLY = 'reply';

    public function __construct(VoterAction $voterAction)
    {
        $this->voterAction = $voterAction;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [VoterAction::DELETE, VoterAction::EDIT, VoterAction::INFORMATIONS, self::REPLY])
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
                return $this->voterAction->canModerate() || $this->canEditTopic($subject, $user);
                break;
            case VoterAction::INFORMATIONS:
                return $this->voterAction->canModerate();
                break;
            case self::REPLY:
                return $this->canReply($subject);
                break;
        }

        return false;
    }

    protected function canEditTopic(Topic $subject, User $user): bool
    {
        return $this->voterAction->canEdit($subject, $user) && $subject->getLocked() !== true;
    }

    protected function canReply(Topic $subject): bool
    {
        return $this->voterAction->canModerate() || $subject->getLocked() !== true;
    }
}
