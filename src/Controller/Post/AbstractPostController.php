<?php

namespace App\Controller\Post;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AbstractPostController extends AbstractController
{
    protected PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    protected function getPost(int $id): Post
    {
        $post = $this->postRepository->findOneById($id);
        if (null === $post) {
            throw $this->createNotFoundException();
        }
        $category = $post->getTopic()->getForum()->getCategory();
        $this->denyAccessUnlessGranted('view', $category);

        return $post;
    }
}
