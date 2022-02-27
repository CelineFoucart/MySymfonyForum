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

    /**
     * Find the default role ROLE_USER.
     */
    public function findDefaultRole(): ?Role
    {
        return $this->findByTitle('ROLE_USER');
    }

    /**
     * Find a role by title, for example ROLE_USER.
     */
    public function findByTitle(string $title): ?Role
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.title = :title')
            ->setParameter('title', $title)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return Role[] Returns an array of Role objects, ROLE_ADMIN and ROLE_MODERATOR 
     */
    public function findTeamRoles(): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.title = :admin')
            ->setParameter('admin', 'ROLE_ADMIN')
            ->orWhere('r.title = :modo')
            ->setParameter('modo', 'ROLE_MODERATOR')
            ->getQuery()
            ->getResult()
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
