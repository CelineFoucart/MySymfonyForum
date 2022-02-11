<?php

namespace App\Tests\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ForumRepository;
use App\Tests\FixtureTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ForumControllerTest extends WebTestCase
{
    use FixtureTrait;

    public function testCategoryPage(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $client->request('GET', $this->getCategoryPath("accueil"));
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Accueil');
    }

    public function testCategoryPageWithNotFound(): void
    {
        $client = static::createClient();
        $client->request('GET', '/category/invalid-1123456789');
        $this->assertResponseStatusCodeSame(404);
    }

    public function testCategoryWithInvalidSlug(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $path = $this->getCategoryPath("accueil");
        $invalidPath = preg_replace("/accueil/", 'acceuillll', $path);
        $client->request('GET', $invalidPath);
        $this->assertResponseRedirects($path);
    }

    public function testForumPage(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $client->request('GET', $this->getForumPath('presentation'));
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Présentation');
    }

    public function testForumPageWithInvalidSlug(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $validPath = $this->getForumPath('presentation');
        $invalidPath = preg_replace("/presentation/", 'prez', $validPath);
        $client->request('GET', $invalidPath);
        $this->assertResponseRedirects($validPath);
    }

    public function testForumWithTopics(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $client->request('GET', $this->getForumPath('presentation'));
        $this->assertSelectorTextNotContains('h3', 'Ce forum ne contient aucun sujet');
    }

    public function testForumNotFound(): void
    {
        $client = static::createClient();
        $client->request('GET', '/forum/invalid-123456789');
        $this->assertResponseStatusCodeSame(404);
    }

    private function getCategoryPath(string $slug): string
    {
        $repo = static::getContainer()->get(CategoryRepository::class);
        $category = $repo->findOneBy(['slug' => $slug]);
        return '/category/' . $category->getSlug() . '-' . $category->getId();
    }
    
    private function getForumPath(string $slug): string
    {
        $repo = static::getContainer()->get(ForumRepository::class);
        $forum = $repo->findOneBy(['slug' => $slug]);
        return '/forum/' . $forum->getSlug() . '-' . $forum->getId();
    }

}
