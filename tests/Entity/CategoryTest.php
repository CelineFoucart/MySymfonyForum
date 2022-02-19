<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use App\Entity\Forum;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertTrue;

class CategoryTest extends TestCase
{
    public function testIsTrue(): void
    {
        $data = $this->getData();
        $category = $this->getCategory($data);
        $this->assertTrue($category->getTitle() === $data['title']);
        $this->assertTrue($category->getSlug() === $data['slug']);
        $this->assertTrue($category->getDescription() === $data['description']);
        $this->assertTrue($category->getOrderNumber() === $data['orderNumber']);
    }

    public function testIsFalse(): void
    {
        $category = $this->getCategory($this->getData());
        $this->assertFalse('False' === $category->getTitle());
        $this->assertFalse('False' === $category->getSlug());
        $this->assertFalse('False' === $category->getDescription());
        $this->assertFalse('False' === $category->getOrderNumber());
    }

    public function testIsEmpty(): void
    {
        $category = new Category();
        $this->assertEmpty($category->getTitle());
        $this->assertEmpty($category->getSlug());
        $this->assertEmpty($category->getDescription());
        $this->assertEmpty($category->getOrderNumber());
    }

    public function testAddForum(): void
    {
        $forum = (new Forum())->setTitle("Lorem")->setSlug("lorem");
        $category = $this->getCategory($this->getData());
        $category->addForum($forum);
        $this->assertTrue($forum === $category->getForums()[0]);
    }

    public function testRemoveForum(): void
    {
        $forum = (new Forum())->setTitle("Lorem")->setSlug("lorem");
        $category = $this->getCategory($this->getData());
        $category->addForum($forum);
        $category->removeForum($forum);
        $this->assertTrue($category->getForums()->isEmpty());
    }

    public function testToString(): void
    {
        $category = $this->getCategory($this->getData());
        $this->assertTrue((string)$category === "Lorem");
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

    private function getCategory(array $data): Category
    {
        return (new Category())
            ->setTitle($data['title'])
            ->setSlug($data['slug'])
            ->setDescription($data['description'])
            ->setOrderNumber($data['orderNumber'])
        ;
    }
}
