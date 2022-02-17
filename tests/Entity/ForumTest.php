<?php

namespace App\Tests\Entity;

use App\Entity\Forum;
use PHPUnit\Framework\TestCase;

class ForumTest extends TestCase
{
    public function testIsTrue(): void
    {
        $data = $this->getData();
        $forum = $this->getForum($data);
        $this->assertTrue($forum->getTitle() === $data['title']);
        $this->assertTrue($forum->getSlug() === $data['slug']);
        $this->assertTrue($forum->getDescription() === $data['description']);
        $this->assertTrue($forum->getOrderNumber() === $data['orderNumber']);
    }

    public function testIsFalse(): void
    {
        $forum = $this->getForum($this->getData());
        $this->assertFalse($forum->getTitle() === 'False');
        $this->assertFalse($forum->getSlug() ===  'False');
        $this->assertFalse($forum->getDescription() ===  'False');
        $this->assertFalse($forum->getOrderNumber() ===  'False');
    } 

    public function testIsEmpty(): void
    {
        $forum = new Forum();
        $this->assertEmpty($forum->getTitle());
        $this->assertEmpty($forum->getSlug());
        $this->assertEmpty($forum->getDescription());
        $this->assertEmpty($forum->getOrderNumber());
    }

    private function getData(): array
    {
        return [
            'title' => "Lorem",
            'slug' => "lorem",
            'description' => "lorem ipsum sit amet",
            'orderNumber' => 1
        ];
    }

    private function getForum(array $data): Forum
    {
        return (new Forum())
            ->setTitle($data['title'])
            ->setSlug($data['slug'])
            ->setDescription($data['description'])
            ->setOrderNumber($data['orderNumber'])
        ;
    }
}
