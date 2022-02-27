<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Form\User\AccountEmailType;
use App\Form\User\AccountType;
use App\Form\User\ProfileType;
use App\Service\ImageManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AccountController extends AbstractController
{
    #[Route('/account', name: 'account')]
    public function index(): Response
    {
        $user = $this->getLoggedUser();
        $userPosts = count($user->getPosts());

        return $this->render('user/account/index.html.twig', [
            'user' => $user,
            'userPosts' => $userPosts,
        ]);
    }

    #[Route('/account/settings', name: 'account_settings')]
    public function settings(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getLoggedUser();
        $emailForm = $this->createForm(AccountEmailType::class, $user);
        $emailForm->handleRequest($request);
        if ($emailForm->isSubmitted() && $emailForm->isValid()) {
            $entityManager->flush();
        }

        $accountForm = $this->createForm(AccountType::class, $user);
        $accountForm->handleRequest($request);
        if ($accountForm->isSubmitted() && $accountForm->isValid()) {
            $entityManager->flush();
        }

        return $this->render('user/account/account_settings.html.twig', [
            'user' => $user,
            'emailForm' => $emailForm->createView(),
            'accountForm' => $accountForm->createView(),
        ]);
    }

    #[Route('/account/profile', name: 'account_profile')]
    public function profile(Request $request, EntityManagerInterface $entityManager, ImageManager $imageManager): Response
    {
        $user = $this->getLoggedUser();
        $profileForm = $this->createForm(ProfileType::class, $user);
        $profileForm->handleRequest($request);
        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            $image = $profileForm->get('avatar')->getData();
            $this->saveAvatar($image, $user, $imageManager);
            $entityManager->flush();
        }

        return $this->render('user/account/account_profile.html.twig', [
            'user' => $user,
            'profileForm' => $profileForm->createView(),
        ]);
    }

    private function getLoggedUser(): User
    {
        return $this->getUser();
    }

    private function saveAvatar(?UploadedFile $image, User $user, ImageManager $imageManager): bool
    {
        if (null !== $image) {
            $errors = $imageManager->setImage($image)->moveImage($user->getId())->getErrors();
            if (empty($errors)) {
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
