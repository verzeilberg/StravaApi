<?php
namespace StravaApi;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [
    'controllers' => [
        'factories' => [
            Controller\StravaController::class => Controller\Factory\StravaControllerFactory::class,
            Controller\StravaImportController::class => Controller\Factory\StravaImportControllerFactory::class,
        ],
        'aliases' => [
            'stravabeheer' => Controller\StravaController::class,
            'stravaimportbeheer' => Controller\StravaImportController::class
        ],
    ],
    'service_manager' => [
        'invokables' => [
            Service\StravaOAuthServiceInterface::class => Service\StravaOAuthService::class,
            Service\StravaServiceInterface::class => Service\StravaService::class,
            Service\StravaDbServiceInterface::class => Service\StravaDbService::class,
            StravaImportLogServiceInterface::class => Service\StravaImportLogService::class,

        ],
    ],
    'view_helpers' => [
        'factories' => [
            View\Helper\StravaApiHelper::class => View\Helper\Factory\StravaViewHelperFactory::class,
        ],
        'aliases' => [
            'stravaViewHelper' => View\Helper\StravaApiHelper::class,
        ],
    ],
    'router' => [
        'routes' => [
            'strava' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/strava[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\StravaController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'stravaimport' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/stravaimport[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\StravaImportController::class,
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
    // The 'access_filter' key is used by the User module to restrict or permit
    // access to certain controller actions for unauthorized visitors.
    'access_filter' => [
        'controllers' => [
            'stravabeheer' => [
                // to anyone.
                ['actions' => '*', 'allow' => '+strava.manage']
            ],
            'stravaimportbeheer' => [
                // to anyone.
                ['actions' => '*', 'allow' => '+strava.manage']
            ],
        ]
    ],
    'view_manager' => [
        'template_path_stack' => [
            'stravaapi' => __DIR__ . '/../view',
        ],
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity']
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ]
    ],
    'asset_manager' => [
        'resolver_configs' => [
            'paths' => [
                __DIR__ . '/../public',
            ],
        ],
    ],
    'stravaSettings' => [
        'clientId' => '',
        'clientSecret' => '',
        'redirectUri' => '',
        'athleteId' => '',
        'code' => '',
        'activitiesPerPage' => 30
    ]
];
