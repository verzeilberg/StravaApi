<?php

namespace StravaApi\Controller\Factory;

use Interop\Container\ContainerInterface;
use StravaApi\Controller\IndexController;
use StravaApi\Entity\Activity;
use StravaApi\Entity\ActivityImportLog;
use StravaApi\Entity\Round;
use StravaApi\Service\StravaService;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * This is the factory for StravaLogController. Its purpose is to instantiate the controller
 * and inject dependencies into its constructor.
 */
class IndexControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $config = $container->get('config');
        $vhm = $container->get('ViewHelperManager');
        $activityRepository = $entityManager->getRepository(Activity::class);
        $roundRepository = $entityManager->getRepository(Round::class);
        $activityImportLogRepository = $entityManager->getRepository(ActivityImportLog::class);
        $stravaService = new StravaService($config, $activityRepository, $roundRepository, $activityImportLogRepository);

        return new IndexController (
            $vhm,
            $stravaService,
            $config
        );
    }

}
