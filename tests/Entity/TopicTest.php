<?php

namespace App\Tests\Entity;

use App\Entity\Forum;
use App\Entity\Topic;
use App\Entity\User;
use DateTime;
use PHPUnit\Framework\TestCase;

class TopicTest extends TestCase
{
    public function testIsTrue(): void
    {
        $data = $this->getData();
        $topic = $this->getTopic($data);
        $this->assertTrue($topic->getTitle() === $data['title']);
        $this->assertTrue($topic->getSlug() === $data['slug']);
        $this->assertTrue($topic->getAuthor() === $data['author']);
        $this->assertTrue($topic->getForum() === $data['forum']);
        $this->assertTrue($topic->getCreated()->format('d/m/Y') === $data['created']->format('d/m/Y'));
    }

    public function testIsFalse(): void
    {
        $topic = $this->getTopic($this->getData());
        $this->assertFalse('False' === $topic->getTitle());
        $this->assertFalse('False' === $topic->getSlug());
        $this->assertFalse('False' === $topic->getForum());
        $this->assertFalse('False' === $topic->getCreated());
        $this->assertFalse('False' === $topic->getAuthor());
    }

    public function testIsEmpty(): void
    {
        $topic = new Topic();
        $this->assertEmpty($topic->getTitle());
        $this->assertEmpty($topic->getSlug());
        $this->assertEmpty($topic->getCreated());
        $this->assertEmpty($topic->getAuthor());
        $this->assertEmpty($topic->getForum());
    }

    private function getData(): array
    {
        return [
            'title' => 'Lorem',
            'slug' => 'lorem',
            'author' => (new User())->setUsername('Ermina'),
            'forum' => (new Forum())->setTitle('Forum')->setSlug('topic'),
            'created' => new DateTime('2021-10-10'),
        ];
    }

    private function getTopic(array $data): Topic
    {
        return (new Topic())
            ->setTitle($data['title'])
            ->setSlug($data['slug'])
            ->setAuthor($data['author'])
            ->setForum($data['forum'])
            ->setCreated($data['created'])
        ;
    }
}
