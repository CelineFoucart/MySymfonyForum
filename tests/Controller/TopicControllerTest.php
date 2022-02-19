<?php

namespace App\Tests\Controller;

use App\Entity\Topic;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
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

    public function testTopicWithInvalidSlug(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $topic = $this->getTopics()[0];
        $path = "/topic/invalid-{$topic->getId()}";
        $client->request('GET', $path);
        $this->assertResponseRedirects();
    }

    public function testTopicReplyWithLoggedUser(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $topic = $this->getTopics()[0];
        $user = static::getContainer()->get(UserRepository::class)->findByPseudo("Ermina");
        $client->loginUser($user);
        $client->request('GET', "/topic/{$topic->getId()}/reply");
        $this->assertResponseIsSuccessful();
    }

    public function testTopicReplyWithNotLoggedUser(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $topic = $this->getTopics()[0];
        $client->request('GET', "/topic/{$topic->getId()}/reply");
        $this->assertResponseRedirects();
    }

    public function testTopicEdit(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $topic = $this->getTopics()[0];
        $user = static::getContainer()->get(UserRepository::class)->findByPseudo("Ermina");
        $client->loginUser($user);
        $client->request('GET', "/topic/{$topic->getId()}/edit");
        $this->assertResponseIsSuccessful();
    }

    public function testTopicEditWithNotLogged(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $topic = $this->getTopics()[0];
        $client->request('GET', "/topic/{$topic->getId()}/edit");
        $this->assertResponseRedirects();
    }

    public function testTopicNew(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $user = static::getContainer()->get(UserRepository::class)->findByPseudo("Ermina");
        $client->loginUser($user);
        $client->request('GET', "/forum/1/new");
        $this->assertResponseIsSuccessful();
    }

    public function testTopicNewNotLogged(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $client->request('GET', "/forum/1/new");
        $this->assertResponseRedirects();
    }

    /**
     * @return Topic[]
     */
    private function getTopics(): array
    {
        $repo = static::getContainer()->get(TopicRepository::class);
        return $repo->findAll();
    }
}
