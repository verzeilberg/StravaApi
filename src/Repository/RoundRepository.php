<?php
namespace StravaApi\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use StravaApi\Entity\Round;

class RoundRepository extends EntityRepository
{
    /**
     * Get the fastest round
     * @param null $type type of activity (exampl. Run or Ride)
     * @param int $workoutType type of workout 1 = Competition 13 = Training
     *
     * @return Round
     */
    public function getFastestRound($type = null, int $workoutType = 1): ? Round
    {
        $qb = $this->createQueryBuilder('r');
        $qb->leftJoin('r.activity', 'a');
        $qb->where('a.workoutType = :workOut');
        $qb->andWhere('r.movingTime > 0');
        if (!empty($type)) {
            $qb->andWhere('a.type = :type');
            $qb->setParameter('type', $type);
        }
        $qb->setParameter('workOut', $workoutType);
        $qb->orderBy('r.movingTime', 'ASC');
        $qb->setMaxResults(1);
        $query = $qb->getQuery();
        $result = $query->getResult();
        return $result[0];
    }

    /**
     * Create a new Round object
     * @return      object
     */
    public function createRound()
    {
        return new Round();
    }

    /**
     * Save round to database
     * @param       $round object
     * @return bool
     * @throws OptimisticLockException
     */
    public function storeRound($round)
    {
        try {
            $this->getEntityManager()->persist($round);
            $this->getEntityManager()->flush();
            return true;
        } catch (\Doctrine\DBAL\DBALException $e) {
            return false;
        }
    }

    /**
     * Remove round from database
     * @param       $round object
     * @return bool
     * @throws OptimisticLockException
     */
    public function removeRound($round)
    {
        try {
            $this->getEntityManager()->remove($round);
            $this->getEntityManager()->flush();
            return true;
        } catch (\Doctrine\DBAL\DBALException $e) {
            return false;
        }
    }
}
