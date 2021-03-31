<?php

namespace App\Repository;

use App\Entity\Search;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function findAllMatches(Search $search){
        $ageMin = $search->getAgeMin();
        $date = new \DateTime('Y');
        $yearMin = ($date->format('Y')) - $ageMin;

        $ageMax = $search->getAgeMax();
        $yearMax = $date->format('Y') - $ageMax;

        $queryBuilder = $this->createQueryBuilder('U');
        //SELECT * FROM user u LEFT JOIN profile p ON u.profile_id=p.id WHERE p.sex='f'
        //SELECT * FROM user u JOIN profile p ON u.profile_id=p.id WHERE p.postal_code="44"
        $queryBuilder->join('U.profile', 'p')
            ->addSelect('p');
        $queryBuilder
            ->andWhere('p.sex = :sex')
            ->andWhere('substr(p.postal_code,1,2) = :dep')
            ->andWhere('YEAR(p.birthday) >= :yearMin')
            ->andWhere('YEAR(p.birthday) <= :yearMax');
        $queryBuilder->setParameter(':sex', $search->getSex());
        $queryBuilder->setParameter(':dep',substr($search->getDepartment(),0,2));
        $queryBuilder->setParameter(':yearMin', $yearMin);
        $queryBuilder->setParameter(':yearMax', $yearMax);

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
