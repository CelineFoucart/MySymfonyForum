<?php

namespace App\Service;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\RoleRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;

class RoleUserService
{
    private RoleRepository $roleRepository;

    private EntityManagerInterface $em;

    public function __construct(RoleRepository $roleRepository, EntityManagerInterface $em)
    {
        $this->roleRepository = $roleRepository;
        $this->em = $em;   
    }
    
    /**
     * Return an array of user role objects
     * @param User $user
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