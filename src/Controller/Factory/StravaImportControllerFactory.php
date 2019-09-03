<?php

namespace StravaApi\Controller\Factory;

use Interop\Container\ContainerInterface;
use StravaApi\Entity\ActivityImportLog;
use StravaApi\Service\StravaImportLogService;
use Zend\ServiceManager\Factory\FactoryInterface;
use StravaApi\Controller\StravaImportController;
use StravaApi\Service\StravaOAuthService;
use StravaApi\Service\StravaService;
use StravaApi\Service\StravaDbService;
use StravaApi\Entity\Round;

/**
 * This is the factory for AuthController. Its purpose is to instantiate the controller
 * and inject dependencies into its constructor.
 */
class StravaImportControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $config = $container->get('config');
        $stravaOAuthService = new StravaOAuthService($config);
        $stravaDbService = new StravaDbService($entityManager);
        $stravaService = new StravaService($config, $stravaDbService);
        $stravaImportLogService = new StravaImportLogService($entityManager);
        $vhm = $container->get('ViewHelperManager');
        $repository = $entityManager->getRepository(Round::class);



        return new StravaImportController(
            $vhm,
            $stravaService,
            $stravaDbService,
            $stravaOAuthService,
            $stravaImportLogService,
            $repository,
            $config
        );
    }

}
