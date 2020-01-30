<?php

namespace StravaApi\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use StravaApi\Entity\AccessToken;

class AccessTokenRepository extends EntityRepository
{

    /**
     * Save access token to database
     * @param $accessToken
     * @return bool
     * @throws OptimisticLockException
     */
    public function storeAccessToken($accessToken)
    {
        try {
            $this->getEntityManager()->persist($accessToken);
            $this->getEntityManager()->flush();
            return true;
        } catch (\Doctrine\DBAL\DBALException $e) {
            return false;
        }
    }

    /**
     * Remove access token from database
     * @param $accessToken
     * @return bool
     * @throws OptimisticLockException
     */
    public function removeAccessToken($accessToken)
    {
        try {
            $this->getEntityManager()->remove($accessToken);
            $this->getEntityManager()->flush();
            return true;
        } catch (\Doctrine\DBAL\DBALException $e) {
            return false;
        }
    }

    /**
     * Check if there are access tokens in the database
     * @return bool
     */
    public function checkForAccessTokens()
    {
        $result = false;
        $accessTokens = $this->findAll();
        if (count($accessTokens) > 0) {
            $result = true;
        }
        return $result;
    }

    /**
     * Get latest access token
     * @return array
     */
    public function getLatestAccessToken()
    {
        return $this->findOneBy([], ['id' => 'DESC'], 1, 0);
    }
}