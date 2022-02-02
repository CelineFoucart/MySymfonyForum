<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ForumController extends AbstractController
{
    private CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    #[Route('/category/{slug}-{id}', name: 'category', requirements:['slug' => '[a-z\-]*'])]
    public function category(int $id, string $slug): Response
    {
        $category = $this->categoryRepository->find($id);
        if($category === null) {
            throw $this->createNotFoundException('Catte catégorie n\'existe pas');
        } elseif ($category->getSlug() !== $slug) {
            return $this->redirectToRoute('category', ['id' => $category->getId(), 'slug' => $category->getSlug()]);
        }

        return $this->render('forum/category.html.twig', [
            'category' => $category,
        ]);
    }
}
