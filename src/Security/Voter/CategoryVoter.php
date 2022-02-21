<?php

namespace App\Security\Voter;

use App\Entity\Category;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CategoryVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [VoterAction::VIEW])
            && $subject instanceof \App\Entity\Category;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        $permissions = self::getUserPermissions($user);

        switch ($attribute) {
            case VoterAction::VIEW:
                return self::canView($subject, $permissions);
                break;
        }

        return false;
    }

    protected static function canView(Category $subject, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if(in_array($permission, $subject->getPermissions())) {
                return true;
            }
        }
        return false;
    }

    protected static function getUserPermissions(?User $user = null): array
    {
        if (!$user instanceof UserInterface) {
            return ["PUBLIC_ACCESS"];
        } 
        return $user->getRoles();
    }

    /**
     * Filter a list of categories with user's permissions
     * 
     * @return Category[]
     */
    public static function filterIndexCategories(array $categories, ?User $user = null): array
    {
        $permissions = self::getUserPermissions($user);
        return array_filter($categories, function(Category $item) use ($permissions) {
            return self::canView($item, $permissions);
        }); 
    }
}
