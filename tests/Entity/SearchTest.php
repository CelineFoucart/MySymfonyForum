<?php

namespace App\Tests\Entity;

use App\Entity\Search;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class SearchTest extends TestCase
{
    public function testIsTrue(): void
    {
        $data = $this->getData();
        $search = $this->getSearch($data);
        $this->assertTrue($search->getType() === $data['type']);
        $this->assertTrue($search->getKeywords() === $data['keywords']);
        $this->assertTrue($search->getUser() === $data['user']);
    }

    public function testIsFalse(): void
    {
        $search = $this->getSearch($this->getData());
        $this->assertFalse($search->getType() === 'False');
        $this->assertFalse($search->getKeywords() ===  'False');
        $this->assertFalse($search->getUser() ===  'False');
    } 

    public function testIsEmpty(): void
    {
        $search = new search();
        $this->assertEmpty($search->getType());
        $this->assertEmpty($search->getKeywords());
        $this->assertEmpty($search->getUser());
    }

    private function getSearch(array $data): Search
    {
        return (new Search())
            ->setType($data['type'])
            ->setUser($data['user'])
            ->setKeywords($data['keywords'])
        ;
    }
    
    private function getData(): array
    {
        return [
            'type' => "topic",
            'user' => (new User())->setUsername("Ermina"),
            'keywords' => "lorem ipsum"
        ];
    }
}
