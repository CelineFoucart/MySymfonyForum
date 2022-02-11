<?php

namespace App\Tests\Controller;

use App\Entity\Topic;
use App\Repository\TopicRepository;
use App\Tests\FixtureTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TopicControllerTest extends WebTestCase
{
    use FixtureTrait;

    public function testTopic(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $topic = $this->getTopics()[0];
        $path = "/topic/{$topic->getSlug()}-{$topic->getId()}";
        $client->request('GET', $path);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', $topic->getTitle());
    }

    /**
     * @return Topic[]
     */
    private function getTopics(): array
    {  
        /** @var TopicRepository */
        $repo = static::getContainer()->get(TopicRepository::class);
        return $repo->findAll();
    }
}
