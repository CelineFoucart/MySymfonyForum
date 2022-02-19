<?php

namespace App\DataFixtures;

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
        $admin = $this->getAdminUser();
        $simpleUser = $this->getSimpleUser();
        $manager->persist($admin);
        $manager->persist($simpleUser);
        $manager->flush();
    }

    private function getAdminUser(): User
    {
        $user = (new User())
            ->setUsername('Ermina')
            ->setEmail('celinefoucart@yahoo.fr')
            ->setCreated(new DateTime())
            ->setRank("grenouille de l'espace")
            ->setLocalisation('dans un futur lointain')
            ->setRoles(['ROLE_ADMIN', 'ROLE_USER'])
        ;
        $password = $this->hasher->hashPassword($user, 'ermina');
        $user->setPassword($password);

        return $user;
    }

    private function getSimpleUser(): User
    {
        $user = (new User())
            ->setUsername('Avalia')
            ->setEmail('avalia@gmail.com')
            ->setCreated(new DateTime())
            ->setRoles(['ROLE_USER'])
        ;
        $password = $this->hasher->hashPassword($user, 'avalia');
        $user->setPassword($password);

        return $user;
    }
}
