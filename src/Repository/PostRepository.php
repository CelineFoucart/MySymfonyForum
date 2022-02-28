<?php

namespace App\Repository;

use App\Entity\Post;
use App\Service\PermissionHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Post|null           find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null           findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]              findAll()
 * @method Post[]              findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Post|null           findOneById($id) find a topic with its user and its topic
 * @method PaginationInterface findPaginated($topicId, $page)
 * @method PaginationInterface search($topicId = null, $page)
 * @method Post[]              findLastThree()
 */
class PostRepository extends ServiceEntityRepository
{
    private PaginatorInterface $paginator;

    private PermissionHelper $permissionHelper;

    private const LIMIT = 15;

    public function __construct(
        ManagerRegistry $registry, 
        PaginatorInterface $paginator,
        PermissionHelper $permissionHelper
    )
    {
        parent::__construct($registry, Post::class);
        $this->permissionHelper = $permissionHelper;
        $this->paginator = $paginator;
    }

    /**
     * Finds a Post by id.
     */
    public function findOneById(int $id): ?Post
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.author', 'u')->addSelect('u')
            ->leftJoin('p.topic', 't')->addSelect('t')
            ->leftJoin('u.defaultRole', 'r')->addSelect('r')
            ->andWhere('p.id = :id')->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Returns the paginated result of Post.
     */
    public function findPaginated(?int $topicId = null, int $page): PaginationInterface
    {
        $builder = $this->createQueryBuilder('p')
            ->select('p')
            ->leftJoin('p.author', 'u')->addSelect('u')
            ->leftJoin('u.defaultRole', 'r')->addSelect('r');
        if (null !== $topicId) {
            $builder->leftJoin('p.topic', 't')->andWhere('t.id = :id')->setParameter('id', $topicId);
        }
        $builder->orderBy('p.id', 'ASC')->orderBy('p.created', 'ASC');

        return $this->getPaginatedQuery($builder, $page);
    }

    /**
     * Returns the result of a search by user id of keywords.
     */
    public function search(
        ?int $userId = null, 
        ?string $keywords = null, 
        int $page = 15,
        array $permissions = []
    ): PaginationInterface {
        $builder = $this->createQueryBuilder('p')
            ->leftJoin('p.author', 'u')->addSelect('u')
            ->leftJoin('p.topic', 't')->addSelect('t')
            ->leftJoin('t.forum', 'f')
            ->leftJoin('f.category', 'c')
            ->leftJoin('u.defaultRole', 'r')->addSelect('r')
        ;
        $builder = $this->permissionHelper->setPermissions($builder, $permissions);
        if (null !== $userId && $userId > 0) {
            $builder->andWhere('u.id = :id')->setParameter('id', $userId);
        }
        if (null !== $keywords) {
            $builder
                ->andWhere($builder->expr()->like('p.content', ':keywords'))
                ->setParameter('keywords', '%'.$keywords.'%');
        }
        $builder->orderBy('p.created', 'DESC');

        return $this->getPaginatedQuery($builder, $page);
    }
    
    /**
     * Finds the last three post posted by users.
     * 
     * @return Post[] Returns an array of Post objects
     */
    public function findLastThree(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.created', 'DESC')
            ->addOrderBy('p.id', 'DESC')
            ->leftJoin('p.author', 'u')->addSelect("u")
            ->leftJoin('u.defaultRole', 'r')->addSelect('r')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }

    /**
     * Paginate a query.
     */
    private function getPaginatedQuery(QueryBuilder $builder, int $page): PaginationInterface
    {
        $items = $this->paginator->paginate(
            $builder->getQuery(),
            $page,
            self::LIMIT
        );

        return $items;
    }
}
