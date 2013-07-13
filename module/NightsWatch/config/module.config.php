<?php

namespace NightsWatch;

return [
    'controllers' => [
        'invokables' => [
            'Site' => 'NightsWatch\Controller\SiteController',
            'Map' => 'NightsWatch\Controller\MapController',
            'Chat' => 'NightsWatch\Controller\ChatController',
            'Join' => 'NightsWatch\Controller\JoinController',
            'Rules' => 'NightsWatch\Controller\RulesController',
        ],
    ],
    'router' => [
        'routes' => [
            'login' => [
                'type' => 'segment',
                'options' => [
                    'route' => '[/]login[/]',
                    'defaults' => [
                        'controller' => 'site',
                        'action' => 'login',
                    ],
                ],
            ],
            'logout' => [
                'type' => 'segment',
                'options' => [
                    'route' => '[/]logout[/]',
                    'defaults' => [
                        'controller' => 'site',
                        'action' => 'logout',
                    ],
                ],
            ],
            'home' => [
                'type' => 'segment',
                'options' => [
                    'route' => '[/][:controller][/:action]',
                    'defaults' => [
                        //'__NAMESPACE__' => 'NightsWatch\Controller',
                        'controller' => 'site',
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
    'service_manager' => [
        'abstract_factories' => [
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ],
        'factories' => [
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
            'right-noauth-navigation' => 'NightsWatch\Navigation\Service\RightNoAuthNavigationFactory',
            'right-auth-navigation' => 'NightsWatch\Navigation\Service\RightAuthNavigationFactory',
        ],
        'aliases' => [
            'translator' => 'MvcTranslator',
        ],
    ],
    'translator' => [
        'locale' => 'en_US',
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ],
        ],
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity'],
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ],
            ],
        ],
    ],
    'navigation' => [
        'default' => [
            [
                'label' => 'Home',
                'route' => 'home',
                'controller' => 'site',
                'action' => 'index',
            ],
            [
                'label' => 'Chat',
                'route' => 'home',
                'controller' => 'chat',
                'action' => 'index',
            ],
            [
                'label' => 'Rules of Engagement',
                'route' => 'home',
                'controller' => 'rules',
            ],
        ],
        'right-noauth' => [
            [
                'label' => 'Log In',
                'route' => 'login',
            ],
            [
                'label' => 'Register',
                'route' => 'home',
                'controller' => 'join',
            ]
        ],
        'right-auth' => [
            [
                'label' => 'Log Out',
                'route' => 'logout',
            ]
        ]
    ],
];
