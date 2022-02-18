<?php

namespace App\Tests\Entity;

use App\Entity\User;
use DateTime;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testIsTrue(): void
    {
        $data = $this->getData();
        $user = $this->setUser();
        $this->assertTrue($user->getUsername() === $data['pseudo']);
        $this->assertTrue($user->getEmail() === $data['email']);
        $this->assertTrue($user->getCreated()->format('d/m/Y') === $data['created']->format('d/m/Y'));
        $this->assertTrue($user->getBirthday()->format('d/m/Y') === $data['birthday']->format('d/m/Y'));
        $this->assertTrue($user->getRank() === $data['rank']);
        $this->assertTrue($user->getLocalisation() === $data['localisation']);
        $this->assertTrue($user->getAvatar() === $data['avatar']);
    }

    public function testIsFalse(): void
    {
        $user = $this->setUser();
        $this->assertFalse('false' === $user->getUsername());
        $this->assertFalse('false' === $user->getEmail());
        $this->assertFalse('false' === $user->getCreated()->format('d/m/Y'));
        $this->assertFalse('false' === $user->getBirthday()->format('d/m/Y'));
        $this->assertFalse('false' === $user->getRank());
        $this->assertFalse('false' === $user->getLocalisation());
        $this->assertFalse('false' === $user->getAvatar());
    }

    public function testIsEmpty(): void
    {
        $user = new User();
        $this->assertEmpty($user->getUsername());
        $this->assertEmpty($user->getEmail());
        $this->assertEmpty($user->getCreated());
        $this->assertEmpty($user->getBirthday());
        $this->assertEmpty($user->getRank());
        $this->assertEmpty($user->getLocalisation());
        $this->assertEmpty($user->getAvatar());
    }

    private function setUser(): User
    {
        $data = $this->getData();

        return (new User())
            ->setUsername($data['pseudo'])
            ->setEmail($data['email'])
            ->setCreated($data['created'])
            ->setBirthday($data['birthday'])
            ->setColor($data['color'])
            ->setRank($data['rank'])
            ->setLocalisation($data['localisation'])
            ->setAvatar($data['avatar'])
        ;
    }

    private function getData(): array
    {
        return [
            'pseudo' => 'John',
            'email' => 'john@email.fr',
            'created' => new DateTime('2021-10-21'),
            'birthday' => new DateTime('1988-12-04'),
            'color' => '#000000',
            'rank' => 'Lorem ipsum',
            'localisation' => 'Creil',
            'avatar' => 'avatar.png',
        ];
    }
}
