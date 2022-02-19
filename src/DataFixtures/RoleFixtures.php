<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * @codeCoverageIgnore
 */
class RoleFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $roles = [
            (new Role())
                ->setTitle('ROLE_USER')
                ->setName('Utilisateurs inscrits')
                ->setColor('#669900'),
            (new Role())
                ->setTitle('ROLE_ADMIN')
                ->setName('Administrateurs')
                ->setColor('#FF9900'),
                (new Role())
                ->setTitle('ROLE_MODERATOR')
                ->setName('Modérateurs généraux')
                ->setColor('#CC3399'),
        ];
        foreach ($roles as $role) {
            $manager->persist($role);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['group1'];
    }
}
