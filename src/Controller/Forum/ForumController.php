<?php

namespace App\Controller\Forum;

use App\Repository\CategoryRepository;
use App\Repository\ForumRepository;
use App\Repository\TopicRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ForumController extends AbstractController
{
    private CategoryRepository $categoryRepository;
    private ForumRepository $forumRepository;
    private TopicRepository $topicRepository;

    public function __construct(
        CategoryRepository $categoryRepository,
        ForumRepository $forumRepository,
        TopicRepository $topicRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->forumRepository = $forumRepository;
        $this->topicRepository = $topicRepository;
    }

    #[Route('/category/{slug}-{id}', name: 'category', requirements: ['slug' => '[a-z\-]*'])]
    public function category(int $id, string $slug): Response
    {
        $category = $this->categoryRepository->find($id);
        if (null === $category) {
            throw $this->createNotFoundException('Cette catégorie n\'existe pas');
        } elseif ($category->getSlug() !== $slug) {
            return $this->redirectToRoute('category', ['id' => $category->getId(), 'slug' => $category->getSlug()]);
        }
        $this->denyAccessUnlessGranted('view', $category, 'Vous ne pouvez pas consulter ce forum');

        return $this->render('forum/category.html.twig', [
            'category' => $category,
            'forums' => $this->forumRepository->findByOrder($category),
        ]);
    }

    #[Route('/forum/{slug}-{id}', name: 'forum', requirements: ['slug' => '[a-z\-]*'])]
    public function forum(int $id, string $slug, Request $request): Response
    {
        $forum = $this->forumRepository->findOneById($id);
        if (null === $forum) {
            throw $this->createNotFoundException('Ce forum n\'existe pas');
        } elseif ($forum->getSlug() !== $slug) {
            return $this->redirectToRoute('forum', ['id' => $forum->getId(), 'slug' => $forum->getSlug()]);
        }
        $this->denyAccessUnlessGranted('view', $forum->getCategory(), 'Vous ne pouvez pas consulter ce forum');

        $page = $request->query->getInt('page', 1);
        $topics = $this->topicRepository->findPaginated($forum->getId(), $page);

        return $this->render('forum/forum.html.twig', [
            'forum' => $forum,
            'topics' => $topics,
        ]);
    }
}
