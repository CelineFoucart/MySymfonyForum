<?php

namespace App\Controller\User;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[Route('/profile/{id}', name: 'profile')]
    public function profile(int $id): Response
    {
        $user = $this->userRepository->find($id);
        if($user === null) {
            throw $this->createNotFoundException("Cet utilisateur n'existe pas !");
        }
        $userPosts = count($user->getPosts());
        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'userPosts' => $userPosts
        ]);
    }

    #[Route('/users', name: 'users_list')]
    public function index(): Response
    {
        $users = $this->userRepository->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users
        ]);
    }
}
