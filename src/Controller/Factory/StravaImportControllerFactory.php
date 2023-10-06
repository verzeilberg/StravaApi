<?php

namespace StravaApi\Controller\Factory;

use Interop\Container\ContainerInterface;
use StravaApi\Entity\AccessToken;
use StravaApi\Entity\Activity;
use StravaApi\Entity\ActivityImportLog;
use StravaApi\Service\StravaAccessTokenService;
use Laminas\ServiceManager\Factory\FactoryInterface;
use StravaApi\Controller\StravaImportController;
use StravaApi\Service\StravaOAuthService;
use StravaApi\Service\StravaService;
use StravaApi\Entity\Round;

/**
 * This is the factory for StravaImportController. Its purpose is to instantiate the controller
 * and inject dependencies into its constructor.
 */
class StravaImportControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $config = $container->get('config');
        $vhm = $container->get('ViewHelperManager');
        $stravaOAuthService = new StravaOAuthService($config);
        $activityRepository = $entityManager->getRepository(Activity::class);
        $roundRepository = $entityManager->getRepository(Round::class);
        $activityImportLogRepository = $entityManager->getRepository(ActivityImportLog::class);
        $stravaService = new StravaService($config, $activityRepository, $roundRepository, $activityImportLogRepository);
        $accessTokenRepository = $entityManager->getRepository(AccessToken::class);
        $accessTokenService = new StravaAccessTokenService($accessTokenRepository);

        return new StravaImportController(
            $vhm,
            $stravaService,
            $stravaOAuthService,
            $accessTokenService,
            $config
        );
    }

}
