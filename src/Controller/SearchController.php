<?php

namespace App\Controller;

use App\Entity\Search;
use App\Form\SearchType;
use App\Repository\PostRepository;
use App\Repository\TopicRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    private PostRepository $postRepository;
    private TopicRepository $topicRepository;

    public function __construct(PostRepository $postRepository, TopicRepository $topicRepository)
    {
        $this->topicRepository = $topicRepository;
        $this->postRepository = $postRepository;
    }

    #[Route('/search', name: 'search')]
    public function search(Request $request): Response
    {
        $search = new Search();
        $form = $this->createForm(SearchType::class, $search, ['method' => 'GET']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $query = [
                'type' => $search->getType(),
                'keywords' => $search->getKeywords(),
            ];
            $user = $search->getUser();
            if (null !== $user) {
                $query['user'] = $search->getUser()->getId();
            }

            return $this->redirect($this->generateUrl('search_result', $query));
        }

        return $this->render('search/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/search/results', name: 'search_result')]
    public function results(Request $request): Response
    {
        $type = $request->query->get('type', 'post');
        $userId = (int) $request->query->get('user');
        $keywords = $request->query->get('keywords');
        $page = $request->query->getInt('page', 1);

        if (!in_array($type, ['post', 'topic'])) {
            $results = [];
        } else {
            if ('topic' === $type) {
                $results = $this->topicRepository->search($userId, $keywords, $page);
            } else {
                $results = $this->postRepository->search($userId, $keywords, $page);
            }
        }

        return $this->render('search/results.html.twig', [
            'results' => $results,
            'type' => $type,
        ]);
    }
}
