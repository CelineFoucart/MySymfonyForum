<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ForumRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ForumController extends AbstractController
{
    private CategoryRepository $categoryRepository;
    private ForumRepository $forumRepository;

    public function __construct(CategoryRepository $categoryRepository, ForumRepository $forumRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->forumRepository = $forumRepository;
    }

    #[Route('/category/{slug}-{id}', name: 'category', requirements:['slug' => '[a-z\-]*'])]
    public function category(int $id, string $slug): Response
    {
        $category = $this->categoryRepository->find($id);
        if($category === null) {
            throw $this->createNotFoundException('Cette catégorie n\'existe pas');
        } elseif ($category->getSlug() !== $slug) {
            return $this->redirectToRoute('category', ['id' => $category->getId(), 'slug' => $category->getSlug()]);
        }

        return $this->render('forum/category.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/forum/{slug}-{id}', name: 'forum', requirements:['slug' => '[a-z\-]*'])]
    public function forum(int $id, string $slug): Response
    {
        $forum = $this->forumRepository->find($id);
        if($forum === null) {
            throw $this->createNotFoundException('Ce forum n\'existe pas');
        } elseif($forum->getSlug() !== $slug) {
            return $this->redirectToRoute('forum', ['id' => $forum->getId(), 'slug' => $forum->getSlug()]);
        }

        return $this->render('forum/forum.html.twig', [
            'forum' => $forum
        ]);
    }
}
