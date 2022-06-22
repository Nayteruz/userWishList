<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Wishes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Wishes>
 *
 * @method Wishes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Wishes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Wishes[]    findAll()
 * @method Wishes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WishesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wishes::class);
    }

    public function getWishList(){
        return $this->createQueryBuilder('w')
            ->join('w.user', 'u')
            ->where('w.deletedAt IS NULL')
            ->orderBy('u.name', 'ASC')
            ->addOrderBy('w.updatedAt', 'DESC')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult()
            ;
            // Пагинацию не делал
    }

    public function getWishListByUser(User $user){
        return $this->createQueryBuilder('w')
            ->join('w.user', 'u')
            ->where('w.deletedAt IS NULL')
            ->andWhere('u.id = :user')
            ->orderBy('w.updatedAt', 'DESC')
            ->setParameter('user', $user)
            ->setMaxResults(20)
            ->getQuery()
            ->getResult()
            ;
    }
}
