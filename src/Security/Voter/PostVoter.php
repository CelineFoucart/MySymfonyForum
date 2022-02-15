<?php

namespace App\Security\Voter;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;

class PostVoter extends Voter
{
    private Security $security;

    const POST_EDIT = 'edit';
    const POST_DELETE = 'delete';

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::POST_EDIT, self::POST_DELETE])
            && $subject instanceof \App\Entity\Post;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }
        if ($this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_MODERATOR')) {
            return true;
        }
        /** @var Post */
        $post = $subject;
        switch ($attribute) {
            case self::POST_EDIT:
                return $this->canEdit($post, $user);
                break;
            case self::POST_DELETE:
                return $this->canDelete($post, $user);
                break;
        }

        return false;
    }

    private function canEdit(Post $post, User $user): bool
    {
        $author = $post->getAuthor();
        if($author === null) {
            return false;
        } else {
            return $user->getId() === $author->getId();
        }
    }

    private function canDelete(Post $post, User $user): bool
    {
        return $this->canEdit($post, $user);
    }
}
