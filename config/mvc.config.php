<?php
$config = [];

$config['templateDir'] = function($container) {
    return realpath(__DIR__.'/../templates');
};

$config['services.skeleton.structure'] = function($container) {
    // I thought long and hard about how to declare dependencies
    // should there be one for each type? controllers, services, models?
    // in the end it's not going to be managed by the api generators,
    // so a generic starting place for people to begin with seemed sensible
    $apiConfig        = file_get_contents($container->get('templateDir').'/apiConfig.txt');
    $index            = file_get_contents($container->get('templateDir').'/index.txt');
    $dependencies     = file_get_contents($container->get('templateDir').'/dependencies.txt');
    $middleware       = file_get_contents($container->get('templateDir').'/middleware.txt');
    $routes           = file_get_contents($container->get('templateDir').'/routes.txt');
    $settings         = file_get_contents($container->get('templateDir').'/settings.txt');
    $bootstrap        = file_get_contents($container->get('templateDir').'/bootstrap.txt');
    $gitignore        = file_get_contents($container->get('templateDir').'/gitignore.txt');
    $composer         = file_get_contents($container->get('templateDir').'/composer.txt');
    $phpunitxml       = file_get_contents($container->get('templateDir').'/phpunitxml.txt');
    $phpunitbootstrap = file_get_contents($container->get('templateDir').'/phpunitbootstrap.txt');

    return [
        'config' => [
            'slim-api.config.php'          => $apiConfig,
        ],
        'migrations' => [],
        'public' => [
            'index.php' => $index
        ],
        'src' => [
            'Controller'       => [
                '.gitkeep' => '',
            ],
            'Model'            => [
                '.gitkeep' => '',
            ],
            'dependencies.php' => $dependencies,
            'middleware.php'   => $middleware,
            'routes.php'       => $routes,
            'settings.php'     => $settings,
            'bootstrap.php'    => $bootstrap,
            'Module.php'       => $module,
        ],
        'tests' => [
            'phpunit' => [
                'bootstrap.php' => $phpunitbootstrap
            ]
        ],
        '.gitignore'       => $gitignore,
        'composer.json'    => $composer,
        'phpunit.xml.dist' => $phpunitxml,
        'README.md'        => 'I\'m a readme, see me roar!'
    ];
};

$config['SlimApi\Skeleton\SkeletonInterface'] = function($container) {
    return new SlimApi\Skeleton\SkeletonService($container->get('services.skeleton.structure'));
};

$config['services.controller'] = function($container) {
    return 'SlimApi\Controller\ControllerService';
};

$config['services.controller.populated'] = function($container) {
    $indexAction     = file_get_contents($container->get('templateDir').'/indexAction.txt');
    $getAction       = file_get_contents($container->get('templateDir').'/getAction.txt');
    $postAction      = file_get_contents($container->get('templateDir').'/postAction.txt');
    $putAction       = file_get_contents($container->get('templateDir').'/putAction.txt');
    $deleteAction    = file_get_contents($container->get('templateDir').'/deleteAction.txt');
    $controllerClass = file_get_contents($container->get('templateDir').'/ControllerClass.txt');
    $controllerCons  = file_get_contents($container->get('templateDir').'/ControllerConstructor.txt');
    $service         = $container->get('services.controller');
    return new $service($indexAction, $getAction, $postAction, $putAction, $deleteAction, $controllerClass, $controllerCons, $container->get('namespace'));
};

$config['services.controller.empty'] = function($container) {
    $indexAction     = file_get_contents($container->get('templateDir').'/emptyIndexAction.txt');
    $getAction       = file_get_contents($container->get('templateDir').'/emptyGetAction.txt');
    $postAction      = file_get_contents($container->get('templateDir').'/emptyPostAction.txt');
    $putAction       = file_get_contents($container->get('templateDir').'/emptyPutAction.txt');
    $deleteAction    = file_get_contents($container->get('templateDir').'/emptyDeleteAction.txt');
    $controllerClass = file_get_contents($container->get('templateDir').'/ControllerClass.txt');
    $service         = $container->get('services.controller');
    return new $service($indexAction, $getAction, $postAction, $putAction, $deleteAction, $controllerClass, '', $container->get('namespace'));
};

$config['SlimApi\Service\DependencyService'] = function($container) {
    return new SlimApi\Service\DependencyService('config/mvc.config.php', file_get_contents($container->get('templateDir').'/ControllerDependency.txt'), file_get_contents($container->get('templateDir').'/ModelDependency.txt'), $container->get('namespace'));
};

$config['SlimApi\Generator\ModelGenerator'] = function($container) {
    return new SlimApi\Generator\ModelGenerator($container->get('SlimApi\Database\DatabaseInterface'), $container->get('SlimApi\Model\ModelInterface'), $container->get('SlimApi\Service\DependencyService'));
};

$config['factory.generator.controller.empty'] = function($container) {
    return new SlimApi\Generator\ControllerGenerator($container->get('services.controller.empty'), $container->get('SlimApi\Service\RouteService'), $container->get('SlimApi\Service\DependencyService'));
};

$config['factory.generator.controller.populated'] = function($container) {
    return new SlimApi\Generator\ControllerGenerator($container->get('services.controller.populated'), $container->get('SlimApi\Service\RouteService'), $container->get('SlimApi\Service\DependencyService'));
};

$config['SlimApi\Generator\ScaffoldGenerator'] = function($container) {
    return new SlimApi\Generator\ScaffoldGenerator($container->get('factory.generator.controller.populated'), $container->get('SlimApi\Generator\ModelGenerator'));
};

$config['SlimApi\Factory\GeneratorFactory'] = function($container) {
    return new SlimApi\Factory\GeneratorFactory([
        'model'      => $container->get('SlimApi\Generator\ModelGenerator'),
        'controller' => $container->get('factory.generator.controller.empty'),
        'scaffold'   => $container->get('SlimApi\Generator\ScaffoldGenerator'),
    ]);
};

return $config;
