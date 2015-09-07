<?php
$container = new Slim\Container;

$container['namespace.root'] = function($container) {
    // alternativly load composer.json?
    return ucfirst(basename(getcwd()));
};

$container['templateDir'] = function($container) {
    return realpath(__DIR__.'/../templates');
};

$container['model.structure'] = function($container) {
    return file_get_contents($container->get('templateDir').'/ModelTemplate.txt');
};

$container['services.model'] = function($container) {
    return new \SlimApi\Model\EloquentModelService($container->get('model.structure'), $container->get('namespace.root'));
};

$container['services.skeleton.structure'] = function($container) {
    // I thought long and hard about to decalre dependencies
    // should there be one for each type? controllers, services, models?
    // in the end it's not going to be managed by the api generators,
    // so a generic starting place for people to begin with seemed sensible
    $dependencies     = file_get_contents($container->get('templateDir').'/dependencies.txt');
    $middleware       = file_get_contents($container->get('templateDir').'/middleware.txt');
    $routes           = file_get_contents($container->get('templateDir').'/routes.txt');
    $settings         = file_get_contents($container->get('templateDir').'/settings.txt');
    $gitignore        = file_get_contents($container->get('templateDir').'/gitignore.txt');
    $composer         = file_get_contents($container->get('templateDir').'/composer.txt');
    $phpunitxml       = file_get_contents($container->get('templateDir').'/phpunitxml.txt');
    $phpunitbootstrap = file_get_contents($container->get('templateDir').'/phpunitbootstrap.txt');

    return [
        'config' => [],
        'migrations' => [],
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

$container['services.skeleton'] = function($container) {
    return new SlimApi\Skeleton\SkeletonService($container->get('services.skeleton.structure'));
};

$container['commands.init'] = function($container) {
    return new SlimApi\Command\InitCommand($container->get('services.skeleton'), $container->get('services.database'));
};

$container['phinxApplication'] = function($container) {
    return new Phinx\Console\PhinxApplication;
};

$container['services.database'] = function($container) {
    return new SlimApi\Database\PhinxService($container->get('phinxApplication'));
};

$container['services.controller'] = function($container) {
    return 'SlimApi\Controller\ControllerService';
};

$container['services.controller.populated'] = function($container) {
    $indexAction     = file_get_contents($container->get('templateDir').'/indexAction.txt');
    $getAction       = file_get_contents($container->get('templateDir').'/getAction.txt');
    $postAction      = file_get_contents($container->get('templateDir').'/postAction.txt');
    $putAction       = file_get_contents($container->get('templateDir').'/putAction.txt');
    $deleteAction    = file_get_contents($container->get('templateDir').'/deleteAction.txt');
    $controllerClass = file_get_contents($container->get('templateDir').'/ControllerClass.txt');
    $controllerCons  = file_get_contents($container->get('templateDir').'/ControllerConstructor.txt');
    $service         = $container->get('services.controller');
    return new $service($indexAction, $getAction, $postAction, $putAction, $deleteAction, $controllerClass, $controllerCons, $container->get('namespace.root'));
};

$container['services.controller.empty'] = function($container) {
    $indexAction     = file_get_contents($container->get('templateDir').'/emptyIndexAction.txt');
    $getAction       = file_get_contents($container->get('templateDir').'/emptyGetAction.txt');
    $postAction      = file_get_contents($container->get('templateDir').'/emptyPostAction.txt');
    $putAction       = file_get_contents($container->get('templateDir').'/emptyPutAction.txt');
    $deleteAction    = file_get_contents($container->get('templateDir').'/emptyDeleteAction.txt');
    $controllerClass = file_get_contents($container->get('templateDir').'/ControllerClass.txt');
    $service         = $container->get('services.controller');
    return new $service($indexAction, $getAction, $postAction, $putAction, $deleteAction, $controllerClass, '', $container->get('namespace.root'));
};

$container['services.route'] = function($container) {
    return new SlimApi\Service\RouteService('src/routes.php', file_get_contents($container->get('templateDir').'/route.txt'), $container->get('namespace.root'));
};

$container['services.dependency'] = function($container) {
    return new SlimApi\Service\DependencyService('src/dependencies.php', file_get_contents($container->get('templateDir').'/ControllerDependency.txt'), file_get_contents($container->get('templateDir').'/ModelDependency.txt'), $container->get('namespace.root'));
};

$container['factory.generator.model'] = function($container) {
    return new SlimApi\Generator\ModelGenerator($container->get('services.database'), $container->get('services.model'), $container->get('services.dependency'));
};

$container['factory.generator.controller.empty'] = function($container) {
    return new SlimApi\Generator\ControllerGenerator($container->get('services.controller.empty'), $container->get('services.route'), $container->get('services.dependency'));
};

$container['factory.generator.controller.populated'] = function($container) {
    return new SlimApi\Generator\ControllerGenerator($container->get('services.controller.populated'), $container->get('services.route'), $container->get('services.dependency'));
};

$container['factory.generator.scaffold'] = function($container) {
    return new SlimApi\Generator\ScaffoldGenerator($container->get('factory.generator.controller.populated'), $container->get('factory.generator.model'));
};

$container['factory.generator'] = function($container) {
    return new SlimApi\Factory\GeneratorFactory([
        'model'      => $container->get('factory.generator.model'),
        'controller' => $container->get('factory.generator.controller.empty'),
        'scaffold'   => $container->get('factory.generator.scaffold'),
    ]);
};

$container['commands.generate'] = function($container) {
    return new SlimApi\Command\GenerateCommand($container->get('factory.generator'));
};

$container['commands'] = function ($container) {
    return [
        'init'     => $container->get('commands.init'),
        'generate' => $container->get('commands.generate'),
    ];
};

$container['application'] = function($container) {
    $application = new \Symfony\Component\Console\Application('SlimApi', '@package_version@');
    $application->addCommands($container->get('commands'));
    return $application;
};

return $container;
