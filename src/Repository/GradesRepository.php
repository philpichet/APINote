<?php

namespace App\Repository;

use App\Entity\Grades;
use App\Entity\Students;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Grades|null find($id, $lockMode = null, $lockVersion = null)
 * @method Grades|null findOneBy(array $criteria, array $orderBy = null)
 * @method Grades[]    findAll()
 * @method Grades[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GradesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Grades::class);
    }


    /**
     * Get the average of a student's grade
     * If there is no not, the average is set to 0 with COALESCE
     * @param Students $student
     * @return int|mixed|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getAverageOfStudent(Students $student)
    {
        return $this->createQueryBuilder("g")
            ->innerJoin("g.student", "s")
            ->select('COALESCE(AVG(g.grade),0) as average')
            ->where("s.id = :id")
            ->setParameter("id", $student->getId())
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Get the average of the class
     * If there is no not, the average is set to 0 with COALESCE
     * @return int|mixed|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getAverageOfAll()
    {
        return $this->createQueryBuilder("g")
            ->innerJoin("g.student", "s")
            ->select('COALESCE(AVG(g.grade),0) as average')
            ->getQuery()
            ->getSingleScalarResult();
    }
    // /**
    //  * @return Grades[] Returns an array of Grades objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Grades
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
