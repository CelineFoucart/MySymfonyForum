<?php

namespace App\Tests\Entity;

use App\Entity\Post;
use App\Entity\Report;
use App\Entity\Topic;
use App\Entity\User;
use DateTime;
use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
    public function testIsTrue(): void
    {
        $data = $this->getData();
        $post = $this->getPost($data);
        $this->assertTrue($post->getTitle() === $data['title']);
        $this->assertTrue($post->getContent() === $data['content']);
        $this->assertTrue($post->getAuthor() === $data['author']);
        $this->assertTrue($post->getTopic() === $data['topic']);
        $this->assertTrue($post->getCreated()->format('d/m/Y') === $data['created']->format('d/m/Y'));
    }

    public function testIsFalse(): void
    {
        $post = $this->getPost($this->getData());
        $this->assertFalse('False' === $post->getTitle());
        $this->assertFalse('False' === $post->getContent());
        $this->assertFalse('False' === $post->getTopic());
        $this->assertFalse('False' === $post->getCreated());
        $this->assertFalse('False' === $post->getAuthor());
    }

    public function testIsEmpty(): void
    {
        $post = new Post();
        $this->assertEmpty($post->getTitle());
        $this->assertEmpty($post->getContent());
        $this->assertEmpty($post->getCreated());
        $this->assertEmpty($post->getAuthor());
        $this->assertEmpty($post->getTopic());
    }

    public function testAddReport(): void
    {
        $post = $this->getPost($this->getData());
        $report = (new Report())->setMessage("Demo");
        $post->addReport($report);
        $this->assertTrue($report === $post->getReports()[0]);
    }

    public function testRemoveReport(): void
    {
        $post = $this->getPost($this->getData());
        $report = (new Report())->setMessage("Demo");
        $post->addReport($report);
        $post->removeReport($report);
        $this->assertTrue($post->getReports()->isEmpty());
    }

    private function getData(): array
    {
        return [
            'title' => 'Lorem',
            'author' => (new User())->setUsername('Ermina'),
            'topic' => (new Topic())->setTitle('Topic')->setSlug('topic'),
            'created' => new DateTime('2021-10-10'),
            'content' => 'lorem ipsum sit amet',
        ];
    }

    private function getPost(array $data): Post
    {
        return (new Post())
            ->setTitle($data['title'])
            ->setAuthor($data['author'])
            ->setTopic($data['topic'])
            ->setCreated($data['created'])
            ->setContent($data['content'])
        ;
    }
}
