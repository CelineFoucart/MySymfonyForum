<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use App\Tests\FixtureTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    use FixtureTrait;

    public function testLoginPageInGet(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Veuillez vous authentifier');
        $this->assertSelectorNotExists('.alert.alert-danger');
    }

    public function testLoginPageWithInvalidData(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton("Connexion")->form([
            'username' => "lol",
            'password' => 'lol'
        ]);
        $client->submit($form);
        $client->followRedirect();
        $this->assertSelectorExists('.alert-danger');
    }

    public function testLoginPageWithValidData(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton("Connexion")->form([
            'username' => "Ermina",
            'password' => 'ermina'
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/');
    }

    public function testLoginPageWithLoggedUser(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $repo = static::getContainer()->get(UserRepository::class);
        $user = $repo->findOneBy(['username' => 'Ermina']);
        $client->loginUser($user);
        $client->request('GET', '/login');
        $this->assertResponseRedirects('/');
    }
}
