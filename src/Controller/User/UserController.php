<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class UserController extends AbstractController
{
    private UserRepository $userRepository;
    private RoleRepository $roleRepository;

    public function __construct(UserRepository $userRepository, RoleRepository $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    #[Route('/profile/{id}', name: 'profile')]
    public function profile(int $id): Response
    {
        $user = $this->userRepository->find($id);
        if (null === $user) {
            throw $this->createNotFoundException("Cet utilisateur n'existe pas !");
        }
        $userPosts = count($user->getPosts());

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'userPosts' => $userPosts,
        ]);
    }

    #[Route('/team', name: 'team')]
    public function team(): Response
    {
        $roles = $this->roleRepository->findTeamRoles();
        $users = $this->userRepository->findTeam();

        $teams = [];
        foreach ($roles as $role) {
            $teams[$role->getTitle()] = [
                'role' => $role,
                'users' => [],
            ];
            $teams[$role->getTitle()]['users'] = array_filter($users, fn (User $user) => $user->hasRole($role->getTitle()));
        }

        return $this->render('user/team.html.twig', [
            'teams' => $teams,
        ]);
    }
}
