<?php

namespace App\Repository;

use App\Entity\Event;
use App\Form\Model\EventFilterModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Event $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Event $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return Event[] Returns an array of Event objects
     */


    public function findByFilters(EventFilterModel $filter): array
    {

        $qb = $this->createQueryBuilder('e');

        if (!is_null($filter->getCampus())) {
            $qb->where('e.campus = :campus')
                ->setParameter('campus', $filter->getCampus());
        }
        if (!empty($filter->getName())) {
            $qb->andWhere('e.name LIKE :name')
                ->setParameter('name', '%'.$filter->getName().'%');
        }
        if (!is_null($filter->getEarliestDate()) && !is_null($filter->getLatestDate())) {
            $qb->andWhere('e.dateTimeStart BETWEEN :earliestDate AND :latestDate')
                ->setParameter('earliestDate', $filter->getEarliestDate())
                ->setParameter('latestDate', $filter->getLatestDate());

        }
        if (!is_null($filter->getEarliestDate()) && is_null($filter->getLatestDate())) {
            $qb->andWhere('e.dateTimeStart > :earliestDate')
                ->setParameter('earliestDate', $filter->getEarliestDate());

        }
        if (!is_null($filter->getLatestDate()) && is_null($filter->getEarliestDate())) {
            $qb->andWhere('e.dateTimeStart < :latestDate')
                ->setParameter('latestDate', $filter->getLatestDate());

        }
        if (!is_null($filter->getPastEvents())) {
            $qb->andWhere('e.status = :past')
                ->setParameter('past', "Ended");

        }
        return $qb->getQuery()->getResult();

    }


    /*
    public function findOneBySomeField($value): ?Event
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
