<?php

namespace App\DataFixtures;

use App\Entity\Forum;
use App\Entity\Post;
use App\Entity\Topic;
use App\Entity\User;
use App\Repository\ForumRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TopicFixtures extends Fixture implements DependentFixtureInterface
{
    private ForumRepository $forumRepository;
    private UserRepository $userRepository;
    private array $users = [];
    private array $forums = [];

    public function __construct(ForumRepository $forumRepository, UserRepository $userRepository)
    {
        $this->forumRepository = $forumRepository;
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();
        $topics = [];
        for ($i=0; $i < 20; $i++) { 
            $length = rand(2,6);
            $topic = (new Topic())
                ->setTitle($faker->words($length, true))
                ->setSlug($faker->slug($length))
                ->setForum($this->getForum())
                ->setCreated(new DateTime())
                ->setAuthor($this->getAuthor())
            ;
            $manager->persist($topic);
            $topics[] = $topic;
        }

        for ($i=0; $i < 150; $i++) { 
            $length = rand(2,6);
            $post = (new Post())
                ->setTitle($faker->words($length, true))
                ->setContent($faker->text())
                ->setCreated(new DateTime())
                ->setAuthor($this->getAuthor())
                ->setTopic($topics[rand(0,19)])
            ;
            $manager->persist($post);
        }
        $manager->flush();
    }

    private function getForum(): Forum
    {
        if(empty($this->forums)) {
            $this->forums = $this->forumRepository->findAll();
        }
        $index = rand(0,2);
        return $this->forums[$index];
    }

    private function getAuthor(): ?User
    {
        if(empty($this->users)) {
            $this->users = $this->userRepository->findAll();
        }
        if((bool)rand(0,1)) {
            return $this->users[rand(0,1)];
        }
        return null;
    }

    public function getDependencies()
    {
        return [
            AppFixtures::class,
            UserFixtures::class
        ];
    }
}
