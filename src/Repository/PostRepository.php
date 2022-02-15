<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    private PaginatorInterface $paginator;

    private const LIMIT = 15;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Post::class);
        $this->paginator = $paginator;
    }

    public function findOneById(int $id): ?Post
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.author', 'u')->addSelect("u")
            ->andWhere('p.id = :id')->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Return the paginated result of Post
     * 
     * @param int|null $topicId
     * @param int $page
     * 
     * @return PaginationInterface
     */
    public function findPaginated(?int $topicId = null, int $page): PaginationInterface
    {
        $builder = $this->createQueryBuilder('p')
            ->select('p')
            ->leftJoin('p.author', 'u')->addSelect("u");
        if($topicId !== null) {
            $builder->leftJoin('p.topic', 't')->andWhere('t.id = :id')->setParameter('id', $topicId);
        }
        $builder->orderBy('p.id', 'ASC')->orderBy('p.created', 'ASC');
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
    public function search(?int $userId = null, ?string $keywords = null, int $page = 15): PaginationInterface
    {
        $builder = $this->createQueryBuilder('p')
            ->leftJoin('p.author', 'u')->addSelect("u")
            ->leftJoin('p.topic', 't')->addSelect('t')
        ;
        if($userId !== null && $userId > 0) {
            $builder->andWhere('u.id = :id')->setParameter('id', $userId);
        }
        if($keywords !== null) {
            $builder
                ->andWhere($builder->expr()->like('p.content', ':keywords'))
                ->setParameter('keywords', '%'. $keywords.'%');  
        }
        $builder->orderBy('p.created', 'DESC');

        return $this->getPaginatedQuery($builder, $page);
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
