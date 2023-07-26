<?php

namespace App\Repository;

use App\Entity\Employee;
use App\Enums\EntitySort;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Employee>
 *
 * @implements PasswordUpgraderInterface<Employee>
 *
 * @method Employee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Employee|null findOneBy(array $criteria, array $orderBy = null)
 * @method Employee[]    findAll()
 * @method Employee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmployeeRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employee::class);
    }

    /**
     * @param Employee $entity
     * @param bool $flush
     * @return void
     */
    public function add(Employee $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param Employee $entity
     * @param bool $flush
     * @return void
     */
    public function remove(Employee $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Employee) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @param string|null $email
     * @param string|null $firstname
     * @param string|null $lastname
     * @param string|null $pesel
     * @param Uuid|null $genderID
     * @param int|null $sort
     * @param \DateTime|null $birthdayFrom
     * @param \DateTime|null $birthdayTo
     * @return Employee[]
     */
    public function searchEmployees(string $email = null, string $firstname = null, string $lastname = null, string $pesel = null, Uuid $genderID = null, int $sort = null, \DateTime $birthdayFrom = null, \DateTime $birthdayTo = null): array
    {

        $qb = $this->createQueryBuilder('e');
        if ($email != null) {
            $qb->andWhere('e.email LIKE :email')
                ->setParameter('email', $email);
        }
        if ($firstname != null) {
            $qb->andWhere('e.firstname LIKE :firstname')
                ->setParameter('firstname', $firstname);
        }
        if ($lastname != null) {
            $qb->andWhere('e.lastname LIKE :lastname')
                ->setParameter('lastname', $lastname);
        }
        if ($pesel != null) {
            $qb->andWhere('e.pesel LIKE :pesel')
                ->setParameter('pesel', $pesel);
        }
        if ($genderID != null) {
            $qb->leftJoin('e.gender', 'g')
                ->andWhere('g.id = :gender')
                ->setParameter('gender', $genderID->toBinary());
        }
        if ($birthdayFrom != null && $birthdayTo != null) {
            $qb->andWhere('(:birthdayFrom >= e.birthday AND :birthdayTo <= e.birthday )')
                ->setParameter('birthdayFrom', $birthdayFrom)
                ->setParameter('birthdayTo', $birthdayTo);
        } elseif ($birthdayFrom) {
            $qb->andWhere('(:birthdayFrom >= e.birthday)')
                ->setParameter('birthdayFrom', $birthdayFrom);
        } elseif ($birthdayTo) {
            $qb->andWhere('(:birthdayTo <= e.birthday)')
                ->setParameter('birthdayTo', $birthdayTo);
        }
        if ($sort != null) {
            switch ($sort) {
                case EntitySort::EMAIL->value:
                {
                    $qb->$qb->orderBy("e.email", "DESC");
                    break;
                }
                case EntitySort::FIRSTNAME->value:
                {
                    $qb->$qb->orderBy("e.firstname", "DESC");
                    break;
                }
                case EntitySort::LASTNAME->value:
                {
                    $qb->$qb->orderBy("e.lastname", "DESC");
                    break;
                }
                case EntitySort::BIRTHDAY->value:
                {
                    $qb->$qb->orderBy("e.birthday", "DESC");
                    break;
                }
                case EntitySort::PESEL->value:
                {
                    $qb->$qb->orderBy("e.pesel", "DESC");
                    break;
                }
                case EntitySort::GENDER->value:
                {
                    $qb->leftJoin('e.gender', 'g')
                        ->groupBy('g')
                        ->orderBy('g.name', "DESC");
                    break;
                }
            }
        }

        $query = $qb->getQuery();

        return $query->execute();
    }
//    /**
//     * @return Employee[] Returns an array of Employee objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Employee
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
