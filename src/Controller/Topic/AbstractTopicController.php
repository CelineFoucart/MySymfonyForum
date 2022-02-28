<?php

namespace App\Controller\Topic;

use App\Entity\Topic;
use App\Repository\PostRepository;
use App\Repository\TopicRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 *  This controller provides userful method for topic controllers.
 * 
 * @author Céline Foucart <celinefoucart@yahoo.fr>
 */
abstract class AbstractTopicController extends AbstractController
{
    protected TopicRepository $topicRepository;
    protected PostRepository $postRepository;

    public function __construct(TopicRepository $topicRepository, PostRepository $postRepository)
    {
        $this->topicRepository = $topicRepository;
        $this->postRepository = $postRepository;
    }

    protected function getTopic(int $id): Topic
    {
        $topic = $this->topicRepository->findOneById($id);
        if (null === $topic) {
            throw $this->createNotFoundException("Ce topic n'existe pas !");
        }
        $category = $topic->getForum()->getCategory();
        $this->denyAccessUnlessGranted('view', $category);

        return $topic;
    }
}
