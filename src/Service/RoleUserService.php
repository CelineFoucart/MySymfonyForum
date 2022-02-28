<?php

namespace App\Service;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class RoleUserService
 * 
 * RoleUserService handles user's roles and persists changes.
 * 
 * @author Céline Foucart <celinefoucart@yahoo.fr>
 */
class RoleUserService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;   
    }
    
    /**
     * Return an array of user role objects.
     * 
     * @param User    $user
     * @param Roles[] $roles
     * 
     * @return Role[]
     */
    public function getUserRoles(User $user, array $roles): array
    {
        $data = [];
        foreach ($roles as $role) {
            if(in_array($role->getTitle(), $user->getRoles()))
            $data[] = $role;
        }
        return $data;
    }

    /**
     * Persists role changes to the database.
     * 
     * @param Role    $defaultRole
     * @param Roles[] $newRoles
     * @param User    $user
     * 
     * @return void
     */
    public function persistRoles(Role $defaultRole, array $newRoles, User $user): void
    {
        if(!in_array($defaultRole, $newRoles)) {
            $newRoles[] = $defaultRole;
        }
        $user->setDefaultRole($defaultRole);
        $titlesRoles = [];
        foreach ($newRoles as $role) {
            $titlesRoles[] = $role->getTitle();
        }
        $user->setRoles($titlesRoles);
        $this->em->flush();
    }
}