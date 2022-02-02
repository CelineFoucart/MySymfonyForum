<?php

namespace App\Tests\Controller;

use App\Tests\FixtureTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    use FixtureTrait;

    public function testHomePageSuccessful(): void
    {
        $client = static::createClient();
        $this->makeFixture();
        $client->request('GET', "/");
        $this->assertResponseIsSuccessful();
    }

    public function testHomePageData(): void 
    {
        $client = static::createClient();
        $this->makeFixture();
        $client->request('GET', "/");
        $this->assertSelectorTextContains('.section-title', "Accueil");
        $this->assertSelectorTextContains(".forum-row-title a", "Présentation");
    }
}
