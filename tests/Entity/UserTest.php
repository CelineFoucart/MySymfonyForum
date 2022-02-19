<?php

namespace App\Tests\Entity;

use App\Entity\Post;
use App\Entity\PrivateMessage;
use App\Entity\Report;
use App\Entity\Topic;
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
        $this->assertTrue($user->getPassword() === $data['password']);
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
        $this->assertFalse('false' === $user->getPassword());
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

    public function testRole(): void
    {
        $roles = ['ROLE_USER', 'ROLE_ADMIN'];
        $user = $this->setUser();
        $user->setRoles($roles);
        $this->assertTrue($user->getRoles() === $roles);
    }

    public function testUserPost(): void
    {
        $user = $user = $this->setUser();
        $post = (new Post())->setContent("Demo");
        $user->addPost($post);
        $this->assertTrue($post === $user->getPosts()[0]);
        $user->removePost($post);
        $this->assertTrue($user->getPosts()->isEmpty());
    }

    public function testUserTopic(): void
    {
        $user = $this->setUser();
        $topic = (new Topic())->setTitle("Demo");
        $user->addTopic($topic);
        $this->assertTrue($topic === $user->getTopics()[0]);
        $user->removeTopic($topic);
        $this->assertTrue($user->getTopics()->isEmpty());
    }

    public function testUserPrivateMessage(): void
    {
        $user = $this->setUser();
        $pm = (new PrivateMessage())->setTitle("Demo");
        $user->addPrivateMessage($pm);
        $this->assertTrue($pm === $user->getPrivateMessages()[0]);
        $user->removePrivateMessage($pm);
        $this->assertTrue($user->getPrivateMessages()->isEmpty());
    }

    public function testUserReceivedPrivateMessage(): void
    {
        $user = $this->setUser();
        $pm = (new PrivateMessage())->setTitle("Demo");
        $user->addReceivedPrivateMessage($pm);
        $this->assertTrue($pm === $user->getReceivedPrivateMessages()[0]);
        $user->removeReceivedPrivateMessage($pm);
        $this->assertTrue($user->getReceivedPrivateMessages()->isEmpty());
    }

    public function testReport(): void
    {
        $user = $this->setUser();
        $report = (new Report())->setMessage("Demo");
        $user->addReport($report);
        $this->assertTrue($report === $user->getReports()[0]);
        $user->removeReport($report);
        $this->assertTrue($user->getReports()->isEmpty());
    }

    private function setUser(): User
    {
        $data = $this->getData();
        return (new User())
            ->setUsername($data['pseudo'])
            ->setEmail($data['email'])
            ->setCreated($data['created'])
            ->setBirthday($data['birthday'])
            ->setRank($data['rank'])
            ->setLocalisation($data['localisation'])
            ->setAvatar($data['avatar'])
            ->setPassword($data['password'])
        ;
    }

    private function getData(): array
    {
        return [
            'pseudo' => 'John',
            'email' => 'john@email.fr',
            'created' => new DateTime('2021-10-21'),
            'birthday' => new DateTime('1988-12-04'),
            'rank' => 'Lorem ipsum',
            'localisation' => 'Creil',
            'avatar' => 'avatar.png',
            'password' => '123pass'
        ];
    }
}
