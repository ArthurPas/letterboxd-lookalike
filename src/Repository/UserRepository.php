<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Laminas\Code\Generator\EnumGenerator\Name;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }
    public function findUser($email)
    {
        return $this->createQueryBuilder('u')
        ->where('u.email like :email')
        ->setParameters(array('email'=>'%'.$email.'%'))
        ->orderBy('u.registerDate', 'DESC')
        ->getQuery()
        ->getResult();
    }

    public function findUserName($id)
    {
        return $this->createQueryBuilder('u')
        ->select('u.name')
        ->where('u.id = :uId')
        ->setParameters(array('uId'=>$id))
        ->getQuery()
        ->getResult();
    }

    public function findUserCountry($id)
    {
        return $this->createQueryBuilder('u')
        ->select('u.country')
        ->where('u.id = :uId')
        ->setParameters(array('uId'=>$id))
        ->getQuery()
        ->getResult();
    }

    public function findUserEmail($id)
    {
        return $this->createQueryBuilder('u')
        ->select('u.email')
        ->where('u.id = :uId')
        ->setParameters(array('uId'=>$id))
        ->getQuery()
        ->getResult();
    }

    public function findUserregisterDate($id)
    {
        return $this->createQueryBuilder('u')
        ->select('u.registerDate')
        ->where('u.id = :uId')
        ->setParameters(array('uId'=>$id))
        ->getQuery()
        ->getResult();
    }
}
