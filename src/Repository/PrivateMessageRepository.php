<?php

namespace App\Repository;

use App\Entity\PrivateMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrivateMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrivateMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrivateMessage[]    findAll()
 * @method PrivateMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method PrivateMessage|null findOneById($id) find a PrivateMessage, its author and its addressee
 */
class PrivateMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrivateMessage::class);
    }
    
    /**
     * Finds a PrivateMessage by id with its author and its addressee.
     */
    public function findOneById(int $id): ?PrivateMessage
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.author', 'u')->select('u')
            ->leftJoin('p.addressee', 'a')->select('p')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
