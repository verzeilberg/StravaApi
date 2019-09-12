<?php
namespace StravaApi\Repository;

use Doctrine\ORM\EntityRepository;
use StravaApi\Entity\Round;

class RoundRepository extends EntityRepository
{

    /**
     * Get fastest round
     * @param $type type of activity (exampl. Run or Ride)
     * @param $workoutType type of workout 1 = Competition 13 = Training
     *
     * @return      array
     */
    public function getFastestRound($type = null, $workoutType = 1)
    {
        $qb = $this->createQueryBuilder('r');
        $qb->leftJoin('r.activity a');
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
     *
     * Create a new Round object
     *
     * @return      object
     *
     */
    public function createRound()
    {
        return new Round();
    }

    /**
     *
     * Save round to database
     *
     * @param       round object
     * @return      void
     *
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
}