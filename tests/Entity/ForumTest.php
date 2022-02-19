<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use App\Entity\Forum;
use App\Entity\Topic;
use COM;
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
        $this->assertFalse('False' === $forum->getTitle());
        $this->assertFalse('False' === $forum->getSlug());
        $this->assertFalse('False' === $forum->getDescription());
        $this->assertFalse('False' === $forum->getOrderNumber());
    }

    public function testIsEmpty(): void
    {
        $forum = new Forum();
        $this->assertEmpty($forum->getTitle());
        $this->assertEmpty($forum->getSlug());
        $this->assertEmpty($forum->getDescription());
        $this->assertEmpty($forum->getOrderNumber());
    }

    public function testSetCategory(): void
    {
        $category = (new Category())->setTitle("Lorem")->setSlug("lorem");
        $forum = $this->getForum($this->getData());
        $forum->setCategory($category);
        $this->assertTrue($category === $forum->getCategory());
    }

    public function testAddTopic(): void
    {
        $forum = $this->getForum($this->getData());
        $topic = (new Topic())->setTitle("Topic 1")->setSlug("topic-1");
        $forum->addTopic($topic);
        $this->assertTrue($topic === $forum->getTopics()[0]);
    }

    public function testRmoveTopic(): void
    {
        $forum = $this->getForum($this->getData());
        $topic = (new Topic())->setTitle("Topic 1")->setSlug("topic-1");
        $forum->addTopic($topic);
        $forum->removeTopic($topic);
        $this->assertTrue($forum->getTopics()->isEmpty());
    }

    private function getData(): array
    {
        return [
            'title' => 'Lorem',
            'slug' => 'lorem',
            'description' => 'lorem ipsum sit amet',
            'orderNumber' => 1,
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
