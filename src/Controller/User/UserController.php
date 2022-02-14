<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Form\AccountEmailType;
use App\Form\AccountType;
use App\Form\ProfileType;
use App\Repository\UserRepository;
use App\Service\ImageManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/account', name: 'account')]
    public function account(Request $request, EntityManagerInterface $entityManager, ImageManager $imageManager): Response
    {
        /** @var User */
        $user = $this->getUser();
        $userPosts = count($user->getPosts());
        if($user === null) {
            return $this->redirectToRoute('app_login');
        }

        $profileForm = $this->createForm(ProfileType::class, $user);
        $profileForm->handleRequest($request);
        if($profileForm->isSubmitted() && $profileForm->isValid()) {
            $image = $profileForm->get('avatar')->getData();
            $this->saveAvatar($image, $user, $imageManager);
            $entityManager->flush();
        }

        $accountForm = $this->createForm(AccountType::class, $user);
        $accountForm->handleRequest($request);
        if($accountForm->isSubmitted() && $accountForm->isValid()) {
            $entityManager->flush();
        }

        $emailForm = $this->createForm(AccountEmailType::class, $user);
        $emailForm->handleRequest($request);
        if($emailForm->isSubmitted() && $emailForm->isValid()) {
            $entityManager->flush();
        }

        return $this->render('user/account.html.twig', [
            'user' => $user,
            'userPosts' => $userPosts,
            'profileForm' => $profileForm->createView(),
            'accountForm' => $accountForm->createView(),
            'emailForm' => $emailForm->createView()
        ]);
    }

    private function saveAvatar(?UploadedFile $image, User $user, ImageManager $imageManager): bool
    {
        if($image !== null) {
            $errors = $imageManager->setImage($image)->moveImage($user->getId())->getErrors();
            if(empty($errors)) {
                $user->setAvatar($imageManager->getFilename());
                return true;
            } else {
                $this->addFlash('error', implode('<br/>', $this->imageManager->getErrors()));
                return false;
            }
        }
        return true;
    }
}
