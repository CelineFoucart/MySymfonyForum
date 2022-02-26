<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    private PaginatorInterface $paginator;

    private const LIMIT = 15;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, User::class);
        $this->paginator = $paginator;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function findPaginated(int $page, string $group = "ROLE_USER"): PaginationInterface
    {
        $builder = $this->createQueryBuilder('u')
            ->leftJoin('u.defaultRole', 'r')
            ->andWhere("u.roles LIKE '%".$group."%'");
        return $this->paginator->paginate(
            $builder->getQuery(),
            $page,
            self::LIMIT
        );
    }
    
    public function findByPseudo(string $pseudo): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.username = :username')
            ->setParameter('username', $pseudo)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
    /**
     * @return User[] Returns an array of Post objects
     */
    public function findLastThree(): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.created', 'DESC')
            ->addOrderBy('u.id', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }
}
