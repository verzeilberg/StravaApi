<?php

namespace StravaApi\Controller\Factory;

use Interop\Container\ContainerInterface;
use StravaApi\Controller\StravaLogController;
use StravaApi\Entity\ActivityImportLog;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * This is the factory for AuthController. Its purpose is to instantiate the controller
 * and inject dependencies into its constructor.
 */
class StravaLogControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $vhm = $container->get('ViewHelperManager');
        $repository = $entityManager->getRepository(ActivityImportLog::class);

        return new StravaLogController(
            $vhm,
            $repository
        );
    }

}
