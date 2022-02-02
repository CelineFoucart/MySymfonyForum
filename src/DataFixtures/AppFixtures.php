<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Forum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $forums = [
            'presentation' => (new Forum())->setTitle("Présentation")->setDescription("Venez vous présenter"),
            'talks' => (new Forum())->setTitle("Causeries")->setDescription("Où l'on discute de tout et de rien"),
            'moderation' => (new Forum())->setTitle("Modération du forum")->setDescription("Où les modérateurs discutent")
        ];
        foreach ($forums as $value) {
            $manager->persist($value);
        }

        $categories = [
            ["Accueil", "Où l'on accueille les nouveaux membres.", $forums['presentation']],
            ["Discussions diverses", "Où l'on parle de tout et de rien", $forums['talks']],
            ["Espace de la modération", null, $forums['moderation']]
        ];
        foreach ($categories as $data) {
            $category = (new Category())->setTitle($data[0])->setDescription($data[1])->addForum($data[2]);
            $manager->persist($category);
        }

        $manager->flush();
    }
}
