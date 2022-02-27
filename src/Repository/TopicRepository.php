<?php

namespace App\Repository;

use App\Entity\Topic;
use App\Service\PermissionHelper;
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

    private PermissionHelper $permissionHelper;

    private const LIMIT = 15;

    public function __construct(
        ManagerRegistry $registry, 
        PaginatorInterface $paginator,
        PermissionHelper $permissionHelper
    ) {
        parent::__construct($registry, Topic::class);
        $this->paginator = $paginator;
        $this->permissionHelper = $permissionHelper;
    }

    /**
     * Returns the paginated result of Topic.
     */
    public function findPaginated(?int $forumId = null, int $page): PaginationInterface
    {
        $builder = $this->createQueryBuilder('t')
            ->leftJoin('t.author', 'u')->addSelect('u')
            ->leftJoin('u.defaultRole', 'r')->addSelect('r');
        if (null !== $forumId) {
            $builder->leftJoin('t.forum', 'f')->andWhere('f.id = :id')->setParameter('id', $forumId);
        }
        $builder->orderBy('t.id', 'ASC')->orderBy('t.created', 'DESC');

        return $this->getPaginatedQuery($builder, $page);
    }

    /**
     * Returns the result of a search by user id of keywords.
     */
    public function search(
            ?int $userId = null, 
            ?string $keywords = null, 
            int $page, 
            array $permissions = []
        ): PaginationInterface {
        $builder = $this->createQueryBuilder('t')
            ->leftJoin('t.author', 'u') ->addSelect('u')
            ->leftJoin('t.forum', 'f')
            ->leftJoin('f.category', 'c');
        
        $builder = $this->permissionHelper->setPermissions($builder, $permissions);
        
        if (null !== $userId && $userId > 0) {
            $builder->andWhere('u.id = :id')->setParameter('id', $userId);
        }
        if (null !== $keywords) {
            $builder
                ->andWhere($builder->expr()->like('t.title', ':keywords'))
                ->setParameter('keywords', '%'.$keywords.'%');
        }
        $builder->orderBy('t.id', 'ASC')->orderBy('t.created', 'ASC');

        return $this->getPaginatedQuery($builder, $page);
    }

    /**
     * Finds a Post by id.
     */
    public function findOneById(int $id): ?Topic
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.author', 'u')->addSelect('u')
            ->leftJoin('t.forum', 'f')->addSelect('f')
            ->leftJoin('u.defaultRole', 'r')->addSelect('r')
            ->where('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
    /**
     * @return Topic[] Returns an array of the last newest Posts
     */
    public function findLastThree(): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.created', 'DESC')
            ->addOrderBy('t.id', 'DESC')
            ->leftJoin('t.author', 'u')->addSelect("u")
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }

    /**
     * Paginates a query.
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
