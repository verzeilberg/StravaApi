<?php

namespace StravaApi\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use StravaApi\Controller\StravaController;
use StravaApi\Service\StravaService;
/*
 * Entities
 */
use StravaApi\Entity\Activity;
use StravaApi\Entity\Round;
use StravaApi\Entity\ActivityImportLog;

/**
 * This is the factory for AuthController. Its purpose is to instantiate the controller
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
        $vhm = $container->get('ViewHelperManager');

        return new StravaController(
            $vhm,
            $stravaService,
            $config
        );
    }

}
