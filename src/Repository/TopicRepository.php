<?php

namespace App\Repository;

use App\Entity\Topic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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

    /**
     * Return the paginated result of Topic
     * 
     * @param int|null $forumId
     * @param int $page
     * 
     * @return PaginationInterface
     */
    public function findPaginated(?int $forumId = null, int $page) : PaginationInterface
    {
        $builder = $this->createQueryBuilder('t')->leftJoin('t.author', 'u')->addSelect("u");
        if($forumId !== null) {
            $builder->leftJoin('t.forum', 'f')->andWhere('f.id = :id')->setParameter('id', $forumId);
        }
        $builder->orderBy('t.id', 'ASC')->orderBy('t.created', 'DESC');
        return $this->getPaginatedQuery($builder, $page);
    }
    
    /**
     * Return the result of a search by user id of keywords
     * 
     * @param int|null $userId
     * @param string|null $keywords
     * @param int $page
     * 
     * @return PaginationInterface
     */
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

        return $this->getPaginatedQuery($builder, $page);
    }

    /**
     * @param int $id
     * 
     * @return Topic|null
     */
    public function findOneById(int $id): ?Topic
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.author', 'u')->addSelect("u")
            ->leftJoin('t.forum', 'f')->addSelect("f")
            ->where('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Paginate a query
     * 
     * @param QueryBuilder $builder
     * @param int $page
     * 
     * @return PaginationInterface
     */
    private function getPaginatedQuery(QueryBuilder $builder,int $page): PaginationInterface
    {
        $items = $this->paginator->paginate(
            $builder->getQuery(),
            $page,
            self::LIMIT
        );
        return $items;
    }
}