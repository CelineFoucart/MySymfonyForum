<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use PHPUnit\Framework\TestCase;

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
        $this->assertFalse($category->getTitle() === 'False');
        $this->assertFalse($category->getSlug() ===  'False');
        $this->assertFalse($category->getDescription() ===  'False');
        $this->assertFalse($category->getOrderNumber() ===  'False');
    }

    public function testIsEmpty(): void
    {
        $category = new Category();
        $this->assertEmpty($category->getTitle());
        $this->assertEmpty($category->getSlug());
        $this->assertEmpty($category->getDescription());
        $this->assertEmpty($category->getOrderNumber());
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

    private function getCategory(array $data): Category
    {
        return (new Category)
            ->setTitle($data['title'])
            ->setSlug($data['slug'])
            ->setDescription($data['description'])
            ->setOrderNumber($data['orderNumber'])
        ;
    }
}
