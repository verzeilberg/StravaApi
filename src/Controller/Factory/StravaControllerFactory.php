<?php

namespace StravaApi\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use StravaApi\Controller\StravaController;
use StravaApi\Service\StravaDbService;

/**
 * This is the factory for AuthController. Its purpose is to instantiate the controller
 * and inject dependencies into its constructor.
 */
class StravaControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $stravaDbService = new StravaDbService($entityManager);
        $vhm = $container->get('ViewHelperManager');

        return new StravaController($vhm, $stravaDbService);
    }

}
