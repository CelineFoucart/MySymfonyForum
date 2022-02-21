<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class StaticticsService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getStats(): array
    {
        $sql = [
            "SELECT count(post.id) AS counts, 'post' AS element FROM post",
            "SELECT count(category.id) AS counts, 'category' AS element FROM category",
            "SELECT count(user.id) AS counts, 'user' AS element FROM user",
            "SELECT count(forum.id) AS counts, 'forum' AS element FROM forum",
            "SELECT count(topic.id) AS counts, 'topic' AS element FROM topic",
            "SELECT count(role.id) AS counts, 'role' AS element FROM role"
        ];
        try {
            $connection = $this->em->getConnection();
            $query = $connection->prepare(join(" UNION ", $sql));
            $data = $query->executeQuery()->fetchAllAssociative();
            return $this->formatStats($data);
        } catch (\Exception $th) {
            return [];
        }
    }

    private function formatStats($stats): array
    {
        if(empty($stats)) {
            return $stats;
        }
        $data = [];
        foreach ($stats as $value) {
            $data[$value['element']] = $value['counts'];
        }
        return $data;
    }
}