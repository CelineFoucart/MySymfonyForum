<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Forum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Forum|null find($id, $lockMode = null, $lockVersion = null)
 * @method Forum|null findOneBy(array $criteria, array $orderBy = null)
 * @method Forum[]    findAll()
 * @method Forum[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ForumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Forum::class);
    }
    
    /**
     * @param int $id
     * 
     * @return Forum|null
     */
    public function findOneById(int $id): ?Forum
    {
        return $this->createQueryBuilder('f')
            ->leftJoin("f.category", 'c')->addSelect('c')
            ->andWhere('f.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return Forum[] Returns an array of Category objects
     */
    public function findByOrder(Category $category): array
    {
        return $this->createQueryBuilder('f')
        ->andWhere('f.category = :category')
        ->setParameter('category', $category)
            ->orderBy('f.orderNumber', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
