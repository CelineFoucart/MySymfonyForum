<?php

namespace App\Twig;

use App\Entity\Topic;
use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class AuthorisationExtension
 * 
 * AuthorisationExtension handles permissions for Twig.
 * 
 * @author Céline Foucart <celinefoucart@yahoo.fr>
 */
class AuthorisationExtension extends AbstractExtension
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('can_edit', [$this, 'canEdit']),
            new TwigFunction('can_moderate', [$this, 'canModerate']),
            new TwigFunction('is_locked', [$this, 'isLocked']),
        ];
    }

    /**
     * @param Topic|Post $topic
     */
    public function canEdit(?User $user, $item): bool
    {
        if ($this->canModerate()) {
            return true;
        }
        if (null === $item->getAuthor() || null === $user) {
            return false;
        }

        return $item->getAuthor()->getId() === $user->getId();
    }

    public function canModerate(): bool
    {
        return $this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_MODERATOR');
    }

    public function isLocked(Topic $topic): bool
    {
        if ($this->canModerate()) {
            return false;
        }

        return $topic->getLocked();
    }
}
