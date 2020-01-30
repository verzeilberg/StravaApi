<?php

namespace StravaApi\Controller\Factory;

use Interop\Container\ContainerInterface;
use StravaApi\Entity\AccessToken;
use StravaApi\Service\StravaAccessTokenService;
use Zend\ServiceManager\Factory\FactoryInterface;
use StravaApi\Controller\StravaController;

/*
 * Services
 */
use StravaApi\Service\StravaService;
use StravaApi\Service\StravaOAuthService;

/*
 * Entities
 */
use StravaApi\Entity\Activity;
use StravaApi\Entity\Round;
use StravaApi\Entity\ActivityImportLog;

/**
 * This is the factory for StravaController. Its purpose is to instantiate the controller
 * and inject dependencies into its constructor.
 */
class StravaControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {


        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $config = $container->get('config');
        $activityRepository = $entityManager->getRepository(Activity::class);
        $roundRepository = $entityManager->getRepository(Round::class);
        $activityImportLogRepository = $entityManager->getRepository(ActivityImportLog::class);
        $stravaService = new StravaService($config, $activityRepository, $roundRepository, $activityImportLogRepository);
        $stravaOAuthService = new StravaOAuthService($config);
        $vhm = $container->get('ViewHelperManager');
        $accessTokenRepository = $entityManager->getRepository(AccessToken::class);
        $accessTokenService = new StravaAccessTokenService($accessTokenRepository);

        return new StravaController(
            $vhm,
            $stravaService,
            $stravaOAuthService,
            $accessTokenService,
            $config
        );
    }

}
