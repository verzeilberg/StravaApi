<?php

namespace StravaApi\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use StravaApi\Controller\StravaController;
use StravaApi\Service\StravaDbService;
use StravaApi\Service\StravaService;
use StravaApi\Service\StravaOAuthService;
use StravaApi\Entity\Activity;

/**
 * This is the factory for AuthController. Its purpose is to instantiate the controller
 * and inject dependencies into its constructor.
 */
class StravaControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $config = $container->get('config');
        $stravaOAuthService = new StravaOAuthService($config);
        $stravaDbService = new StravaDbService($entityManager);
        $stravaService = new StravaService($config, $stravaDbService);
        $vhm = $container->get('ViewHelperManager');
        $activityRepository = $entityManager->getRepository(Activity::class);

        return new StravaController($vhm, $stravaDbService, $stravaService, $stravaOAuthService, $config, $activityRepository);
    }

}
