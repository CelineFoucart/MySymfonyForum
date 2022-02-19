<?php

namespace App\Repository;

use App\Entity\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Role|null find($id, $lockMode = null, $lockVersion = null)
 * @method Role|null findOneBy(array $criteria, array $orderBy = null)
 * @method Role[]    findAll()
 * @method Role[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }

    public function findDefaultRole(): ?Role
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.title = :title')
            ->setParameter('title', 'ROLE_USER')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return Role[] Returns an array of Role objects
     */
    public function findForPublicUse(): array
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
