<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Forum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * @codeCoverageIgnore
 */
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $forums = [
            'presentation' => (new Forum())
                ->setTitle('Présentation')
                ->setDescription('Venez vous présenter')
                ->setSlug('presentation')
                ->setOrderNumber(1),
            'talks' => (new Forum())
                ->setTitle('Causeries')
                ->setDescription("Où l'on discute de tout et de rien")
                ->setSlug('causeries')
                ->setOrderNumber(1),
            'moderation' => (new Forum())
                ->setTitle('Modération du forum')
                ->setDescription('Où les modérateurs discutent')
                ->setSlug('moderation')
                ->setOrderNumber(1),
        ];
        foreach ($forums as $value) {
            $manager->persist($value);
        }

        $categories = [
            ['Accueil', "Où l'on accueille les nouveaux membres.", 'accueil', 1, $forums['presentation']],
            ['Discussions diverses', "Où l'on parle de tout et de rien", 'discussions-diverses', 2, $forums['talks']],
            ['Espace de la modération', 'Où les modérateurs discutent', 'espace-de-la-moderation', 3, $forums['moderation']],
        ];
        foreach ($categories as $data) {
            $category = (new Category())
                ->setTitle($data[0])
                ->setDescription($data[1])
                ->setSlug($data[2])
                ->setOrderNumber($data[3])
                ->addForum($data[4]);

            if ('Accueil' === $category->getTitle()) {
                $category->setPermissions(['PUBLIC_ACCESS', 'ROLE_USER']);
            } else {
                $category->setPermissions(['ROLE_USER', 'ROLE_ADMIN']);
            }
            $manager->persist($category);
        }

        $manager->flush();
    }
}
