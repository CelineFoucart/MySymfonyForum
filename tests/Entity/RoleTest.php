<?php

namespace App\Tests\Entity;

use App\Entity\Role;
use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase
{
    public function testIsTrue(): void
    {
        $data = $this->getData();
        $role = $this->getEntity();
        $this->assertTrue($role->getTitle() === $data['title']);
        $this->assertTrue($role->getName() === $data['name']);
        $this->assertTrue($role->getDescription() === $data['description']);
        $this->assertTrue($role->getColor() === $data['color']);
    }

    public function testIsFalse(): void
    {
        $role = $this->getEntity();
        $this->assertFalse($role->getTitle() === 'False');
        $this->assertFalse($role->getName() === 'False');
        $this->assertFalse($role->getDescription() === 'False');
        $this->assertFalse($role->getColor() === 'False');
    }

    public function testIsEmpty(): void
    {
        $role = new role();
        $this->assertEmpty($role->getTitle());
        $this->assertEmpty($role->getName());
        $this->assertEmpty($role->getDescription());
        $this->assertEmpty($role->getColor());
    }
    

    private function getData(): array
    {
        return [
            'title' => 'ROLE_USER',
            'name' => 'utilisateur',
            'description' => "lorem lorem",
            'color' => "#000",
        ];
    }

    private function getEntity(): Role
    {
        $data =$this->getData();
        return (new Role())
            ->setTitle($data['title'])
            ->setName($data['name'])
            ->setDescription($data['description'])
            ->setColor($data['color']);
    }
}
