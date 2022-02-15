<?php

namespace App\Repository;

use App\Entity\Topic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Topic|null find($id, $lockMode = null, $lockVersion = null)
 * @method Topic|null findOneBy(array $criteria, array $orderBy = null)
 * @method Topic[]    findAll()
 * @method Topic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TopicRepository extends ServiceEntityRepository
{
    private PaginatorInterface $paginator;

    private const LIMIT = 15;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Topic::class);
        $this->paginator = $paginator;
    }
    
    public function search(?int $userId = null, ?string $keywords = null, int $page): PaginationInterface
    {
        $builder = $this->createQueryBuilder('t')->leftJoin('t.author', 'u')->addSelect("u");
        if($userId !== null && $userId > 0) {
            $builder->andWhere('u.id = :id')->setParameter('id', $userId);
        }
        if($keywords !== null) {
            $builder
                ->andWhere($builder->expr()->like('t.title', ':keywords'))
                ->setParameter('keywords', '%'. $keywords.'%');  
        }
        $builder->orderBy('t.id', 'ASC')->orderBy('t.created', 'DESC');

        $topics = $this->paginator->paginate(
            $builder->getQuery(),
            $page,
            self::LIMIT
        );

        return $topics;
    }
}
