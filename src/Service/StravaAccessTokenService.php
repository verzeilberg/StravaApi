<?php

namespace StravaApi\Service;

use Doctrine\ORM\OptimisticLockException;
use Exception;
use StravaApi\Entity\AccessToken;
use StravaApi\Entity\Activity;
use StravaApi\Repository\AccessTokenRepository;
use User\View\Helper\CurrentUser;
use Laminas\ServiceManager\ServiceLocatorInterface;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Laminas\Paginator\Paginator;
use Polyline;

/*
 * Repositories
 */
use StravaApi\Repository\ActivityRepository;
use StravaApi\Repository\RoundRepository;
use StravaApi\Repository\ActivityImportLogRepository;

class StravaAccessTokenService
{


    /*
     * @var AccessTokenRepository
     */
    public $accessTokenRepository;

    public function __construct(
        AccessTokenRepository $accessTokenRepository
    )
    {
        $this->accessTokenRepository = $accessTokenRepository;
    }


    /**
     * Create new access token and set data
     * @param $data
     * @return bool
     * @throws OptimisticLockException
     */
    public function createAccessToken($data)
    {
        $expiresAt = new \DateTime();
        $expiresAt->setTimestamp($data->expires_at);

        $expiresIn = new \DateTime();
        $expiresIn->setTimestamp($data->expires_in);

        $accessToken =  new AccessToken();
        $accessToken->setExpiresAt($expiresAt);
        $accessToken->setExpiresIn($expiresIn);
        $accessToken->setRefreshToken($data->refresh_token);
        $accessToken->setAccessToken($data->access_token);
        return $this->accessTokenRepository->storeAccessToken($accessToken);
    }

    /**
     * Check if acces token is valid
     * @param $accesToken
     * @return bool
     * @throws Exception
     */
    public function checkAccessTokenVality($accesToken)
    {
        $result = true;
        $currentDate = new \DateTime();

        $accesTokenDate = new \DateTime($accesToken->getExpiresAt()->format('Y-m-d') .' ' .$accesToken->getExpiresIn()->format('H:i:s'));

        if($accesTokenDate < $currentDate) {
            $result = false;
        }

        return $result;
    }

}
