<?php

namespace App\Tests\Subscriber;

use App\Entity\Role;
use App\Entity\User;
use App\EventSubscriber\EasyAdminSubscriber;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\RoleRepository;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class EasyAdminSubscriberTest extends TestCase
{

    private EasyAdminSubscriber $subscriber;

    protected function setUp(): void
    {
        $em = $this->getMockBuilder(EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
        $hasher = $this->getMockBuilder(UserPasswordHasherInterface::class)->disableOriginalConstructor()->getMock();
        $repo = $this->getMockBuilder(RoleRepository::class)->disableOriginalConstructor()->getMock();
        $this->subscriber = new EasyAdminSubscriber($em, $hasher, $repo); 
    }
    

    public function testDeleteAdminUser(): void
    {
        $this->expectException(AccessDeniedException::class);
        $user = (new User("John"))->setRoles(['ROLE_ADMIN']);
        $event = new BeforeEntityDeletedEvent($user);
        $this->subscriber->canDelete($event);
    }

    public function testDeleteSimpleUser(): void
    {
        $user = (new User("John"))->setRoles(['ROLE_USER']);
        $event = new BeforeEntityDeletedEvent($user);
        $this->subscriber->canDelete($event);
        $this->assertTrue(TRUE);
    }

    public function testDeleteAdminRole(): void
    {
        $this->testRoleDeletion("ROLE_ADMIN");
    }

    public function testDeleteModeratorRole(): void
    {
        $this->testRoleDeletion("ROLE_MODERATOR");
    }

    public function testDeleteSimpleUserRole(): void
    {
        $this->testRoleDeletion("ROLE_USER");
    }

    public function testDeletionWithNotForbiddenRole(): void
    {
        $role = (new Role())->setTitle("ROLE_READER");
        $event = new BeforeEntityDeletedEvent($role);
        $this->subscriber->canDelete($event);
        $this->assertTrue(TRUE);
    }

    private function testRoleDeletion(string $roleName)
    {
        $this->expectException(AccessDeniedException::class);
        $role = (new Role())->setTitle($roleName);
        $event = new BeforeEntityDeletedEvent($role);
        $this->subscriber->canDelete($event);
    }
}
