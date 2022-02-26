<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @codeCoverageIgnore
 */
class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $roles = $this->getRoles();
        foreach ($roles as $role) {
            $manager->persist($role);
        }

        $admin = $this->getAdminUser($roles['admin']);
        $modoUser = $this->getModoUser($roles['modo']);
        $simpleUser = $this->getSimpleUser($roles['user']);
        $manager->persist($admin);
        $manager->persist($modoUser);
        $manager->persist($simpleUser);
        $manager->flush();
    }

    private function getAdminUser(Role $defaultRole): User
    {
        $user = (new User())
            ->setUsername('Ermina')
            ->setEmail('celinefoucart@yahoo.fr')
            ->setCreated(new DateTime())
            ->setRank("grenouille de l'espace")
            ->setLocalisation('dans un futur lointain')
            ->setRoles(['ROLE_ADMIN', 'ROLE_USER'])
            ->setAvatar("avatar-3.jpg")
            ->setDefaultRole($defaultRole)
        ;
        $password = $this->hasher->hashPassword($user, 'ermina');
        $user->setPassword($password);

        return $user;
    }

    private function getModoUser(Role $defaultRole): User
    {
        $user = (new User())
            ->setUsername('Avalia')
            ->setEmail('avalia@gmail.com')
            ->setRank('Magicienne guerrière')
            ->setLocalisation('Avec le Gardien')
            ->setAvatar("avatar-4.jpg")
            ->setCreated(new DateTime())
            ->setRoles(['ROLE_MODERATOR', 'ROLE_USER'])
            ->setDefaultRole($defaultRole)
        ;
        $password = $this->hasher->hashPassword($user, 'avalia');
        $user->setPassword($password);

        return $user;
    }

    private function getSimpleUser(Role $defaultRole): User
    {
        $user = (new User())
            ->setUsername('Gabriel')
            ->setCreated(new DateTime())
            ->setRoles(['ROLE_USER'])
            ->setDefaultRole($defaultRole)
            ->setEmail('gabriel@gmail.com')
        ;
        $password = $this->hasher->hashPassword($user, 'gabriel');
        $user->setPassword($password);

        return $user;
    }

    private function getRoles(): array
    {
        return $roles = [
            'user' => (new Role())
                ->setTitle('ROLE_USER')
                ->setName('Utilisateurs inscrits')
                ->setColor('#669900'),
            'admin' => (new Role())
                ->setTitle('ROLE_ADMIN')
                ->setName('Administrateurs')
                ->setColor('#FF9900'),
            'modo' => (new Role())
                ->setTitle('ROLE_MODERATOR')
                ->setName('Modérateurs généraux')
                ->setColor('#CC3399'),
        ];
    }
}
