<?php

namespace App\Repository;

use App\Entity\Report;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Report|null find($id, $lockMode = null, $lockVersion = null)
 * @method Report|null findOneBy(array $criteria, array $orderBy = null)
 * @method Report[]    findAll()
 * @method Report[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Forum|null  findReportById($id) find a Report and its post
 * @method Report[]     findPostReports()
 * @method Report[]     findLastReports()
 */
class ReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Report::class);
    }

    /**
     * @return Report[] Returns an array of Report objects
     */
    public function findPostReports(string $type = 'post'): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.type = :type')
            ->leftJoin('r.post', 'p')
            ->setParameter('type', $type)
            ->orderBy('r.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Report[] Returns the last five Reports.
     */
    public function findLastReports(string $type = 'post'): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.type = :type')
            ->leftJoin('r.post', 'p')
            ->setParameter('type', $type)
            ->orderBy('r.id', 'DESC')
            ->getQuery()
            ->setMaxResults(5)
            ->getResult()
        ;
    }

    /**
     * Finds a report by id with its post.
     */
    public function findReportById(int $id): ?Report
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.id = :id')
            ->leftJoin('r.post', 'p')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
