<?php
/*
 * @author Yesser Bkhouch <yesserbakhouch@hotmail.com>
 */

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\Customer;
use App\Entity\Provider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 *
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /**
     * Find bookings for a specific customer.
     *
     * @param array $statusFilter Optional status filter
     *
     * @return Booking[]
     */
    public function findByCustomer(Customer $customer, array $statusFilter = []): array
    {
        $qb = $this->createQueryBuilder('b')
            ->andWhere('b.customer = :customer')
            ->setParameter('customer', $customer);

        if (!empty($statusFilter)) {
            $qb->andWhere('b.status IN (:statuses)')
                ->setParameter('statuses', $statusFilter);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Find bookings for a specific provider.
     *
     * @param array $statusFilter Optional status filter
     *
     * @return Booking[]
     */
    public function findByProvider(Provider $provider, array $statusFilter = []): array
    {
        $qb = $this->createQueryBuilder('b')
            ->andWhere('b.provider = :provider')
            ->setParameter('provider', $provider);

        if (!empty($statusFilter)) {
            $qb->andWhere('b.status IN (:statuses)')
                ->setParameter('statuses', $statusFilter);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Find active bookings within a specific date range.
     *
     * @param array $statusFilter Optional status filter
     *
     * @return Booking[]
     */
    public function findActiveBookings(\DateTimeInterface $start, \DateTimeInterface $end, array $statusFilter = []): array
    {
        $qb = $this->createQueryBuilder('b')
            ->andWhere('b.startTime <= :end')
            ->andWhere('b.endTime >= :start')
            ->setParameter('start', $start)
            ->setParameter('end', $end);

        if (!empty($statusFilter)) {
            $qb->andWhere('b.status IN (:statuses)')
                ->setParameter('statuses', $statusFilter);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Find unreviewd bookings for a customer.
     *
     * @return Booking[]
     */
    public function findUnreviewedBookings(Customer $customer): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.customer = :customer')
            ->andWhere('b.status = :completedStatus')
            ->andWhere('b.isReviewed = :isReviewed')
            ->setParameter('customer', $customer)
            ->setParameter('completedStatus', 'completed')
            ->setParameter('isReviewed', false)
            ->getQuery()
            ->getResult();
    }
}
