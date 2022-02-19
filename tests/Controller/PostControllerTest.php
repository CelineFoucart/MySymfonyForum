<?php

namespace App\Tests\Controller;

use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Tests\FixtureTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    use FixtureTrait;

    public function testEditPostWihLoginUser(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $user = static::getContainer()->get(UserRepository::class)->findByPseudo("Ermina");
        $client->loginUser($user);
        $client->request('GET', '/post/1/edit');
        $this->assertResponseIsSuccessful();
    }

    public function testEditPostNotFound(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $user = static::getContainer()->get(UserRepository::class)->findByPseudo("Ermina");
        $client->loginUser($user);
        $client->request('GET', '/post/87897412/edit');
        $this->assertResponseStatusCodeSame(404);
    }

    public function testEditPosWihNotLoggedUser(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $client->request('GET', '/post/1/edit');
        $this->assertResponseRedirects();
    }

    public function testSubmitValid(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        static::getContainer()->get(PostRepository::class)->find(1);
        $user = static::getContainer()->get(UserRepository::class)->findByPseudo("Ermina");
        $client->loginUser($user);
        $crawler = $client->request('GET', '/post/1/edit');
        $form = $crawler->selectButton("Envoyer")->form([
            'post[title]' => "Lorem ipsum",
            'post[content]' => "Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum"
        ]);
        $client->submit($form);
        $this->assertResponseRedirects();
    }

    public function testSubmitInvalid(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $user = static::getContainer()->get(UserRepository::class)->findByPseudo("Ermina");
        $client->loginUser($user);
        $crawler = $client->request('GET', '/post/1/edit');
        $form = $crawler->selectButton("Envoyer")->form([
            'post[title]' => "",
            'post[content]' => ""
        ]);
        $client->submit($form);
        $this->assertSelectorExists('.invalid-feedback');
    }

    public function testReportWihLoginUser(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $user = static::getContainer()->get(UserRepository::class)->findByPseudo("Ermina");
        $client->loginUser($user);
        $client->request('GET', '/post/1/report');
        $this->assertResponseIsSuccessful();
    }

    public function testSubmitReportValid(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        static::getContainer()->get(PostRepository::class)->find(1);
        $user = static::getContainer()->get(UserRepository::class)->findByPseudo("Ermina");
        $client->loginUser($user);
        $crawler = $client->request('GET', '/post/1/report');
        $form = $crawler->selectButton("Envoyer")->form([
            'report[message]' => "Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum"
        ]);
        $client->submit($form);
        $this->assertResponseRedirects();
    }

    public function testSubmitReportInvalid(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        static::getContainer()->get(PostRepository::class)->find(1);
        $user = static::getContainer()->get(UserRepository::class)->findByPseudo("Ermina");
        $client->loginUser($user);
        $crawler = $client->request('GET', '/post/1/report');
        $form = $crawler->selectButton("Envoyer")->form([
            'report[message]' => ""
        ]);
        $client->submit($form);
        $this->assertSelectorExists('.invalid-feedback');
    }

    public function testDeletetWihLoginUser(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $user = static::getContainer()->get(UserRepository::class)->findByPseudo("Ermina");
        $client->loginUser($user);
        $client->request('GET', '/post/1/delete');
        $this->assertResponseIsSuccessful();
    }
}
