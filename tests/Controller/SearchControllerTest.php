<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use App\Tests\FixtureTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SearchControllerTest extends WebTestCase
{
    use FixtureTrait;

    public function testSearchIndex(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $client->request('GET', '/search');
        $this->assertSelectorTextContains('h2', 'Recherche dans le forum');
    }

    public function testSearchIndexWithValidSubmit(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $crawler = $client->request('GET', '/search');
        $form = $crawler->selectButton("Rechercher")->form([
            "search[type]" => "post",
            "search[user]" => "2"
        ]);
        $client->submit($form);
        $this->assertResponseRedirects();
    }

    public function testResultPostWithUser(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        /** @var UserRepository */
        $repo = static::getContainer()->get(UserRepository::class);
        $userId = $repo->findByPseudo("Ermina")->getId();
        $client->request('GET', '/search/results?type=post&user='.$userId);
        $this->assertSelectorTextContains('.fw-bold', 'Ermina');
        $this->assertSelectorTextNotContains('.fw-bold', "Aucun résultat ne correspond aux termes que vous avez spécifiés.");
    }

    public function testResultTopicWithUser(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        /** @var UserRepository */
        $repo = static::getContainer()->get(UserRepository::class);
        $userId = $repo->findByPseudo("Ermina")->getId();
        $client->request('GET', '/search/results?type=topic&user='.$userId);
        $this->assertSelectorTextContains('.fw-bold', 'Ermina');
        $this->assertSelectorTextNotContains('.fw-bold', "Aucun résultat ne correspond aux termes que vous avez spécifiés.");
    }

    public function testResultWithInvalidUserId(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $client->request('GET', '/search/results?type=post&user=123456789');
        $this->assertSelectorTextContains('.fw-bold', "Aucun résultat ne correspond aux termes que vous avez spécifiés.");
    }

    public function testResultWithInvalidType(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $client->request('GET', '/search/results?type=lolert');
        $this->assertSelectorTextContains('.fw-bold', "Aucun résultat ne correspond aux termes que vous avez spécifiés.");
    }
}
