<?php

namespace App\Controller\User;

use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *  Controller used to manage the users' lists.
 * 
 * @author Céline Foucart <celinefoucart@yahoo.fr>
 */
final class GroupController extends AbstractController
{
    private UserRepository $userRepository;
    private RoleRepository $roleRepository;

    public function __construct(UserRepository $userRepository, RoleRepository $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    #[Route('/users', name: 'users_list')]
    public function index(Request $request): Response
    {
        $users = $this->userRepository->findPaginated($request->query->getInt('page', 1));
        $roles = $this->roleRepository->findForPublicUse();

        return $this->render('user/group/index.html.twig', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    #[Route('/users/group/{id}', name: 'users_group')]
    public function group(int $id, Request $request): Response
    {
        $role = $this->roleRepository->find($id);
        if (null === $role) {
            throw $this->createNotFoundException('Ce groupe n\'existe pas.');
        }
        $page = $request->query->getInt('page', 1);
        $users = $this->userRepository->findPaginated($page, $role->getTitle());

        return $this->render('user/group/group.html.twig', [
            'role' => $role,
            'users' => $users,
        ]);
    }
}
