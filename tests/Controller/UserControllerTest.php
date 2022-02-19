<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\FixtureTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    use FixtureTrait;

    public function testUserList(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $client->request('GET', '/users');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.forum-section', 'Ermina');
    }

    public function testUserProfilWithNotLoggedUser(): void 
    {
        $client = static::createClient();
        $this->makeFixture();
        $user = $this->getUser("Ermina");
        $client->request('GET', '/profile/' . $user->getId());
        $this->assertResponseRedirects('/login');
    }

    public function testUserProfilWithValidUser(): void 
    {
        $client = static::createClient();
        $this->makeFixture();
        $user = $this->getUser("Ermina");
        $client->loginUser($user);
        $client->request('GET', '/profile/' . $user->getId());
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.profile-details', 'Ermina');
    }

    public function testUserProfilWithInvalidUser(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getUser("Ermina"));
        $client->request('GET', '/profile/123456789789123466');
        $this->assertResponseStatusCodeSame(404);
    }

    private function getUser(string $pseudo): User
    {
        $repo = static::getContainer()->get(UserRepository::class);
        return $repo->findOneBy(['username' => $pseudo]);
    }
}
