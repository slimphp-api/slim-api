<?php
$container = new Pimple\Container;

$container['namespace.root'] = function($container) {
    // alternativly load composer.json?
    return ucfirst(basename(getcwd()));
};

$container['templateDir'] = function($container) {
    return realpath(__DIR__.'/../templates');
};

$container['model.structure'] = function($container) {
    return file_get_contents($container['templateDir'].'/ModelTemplate.txt');
};

$container['services.model'] = function($container) {
    return new \SlimApi\Model\EloquentModelService($container['model.structure'], $container['namespace.root']);
};

$container['services.skeleton.structure'] = function($container) {
    // I thought long and hard about to decalre dependencies
    // should there be one for each type? controllers, services, models?
    // in the end it's not going to be managed by the api generators,
    // so a generic starting place for people to begin with seemed sensible
    $dependencies     = file_get_contents($container['templateDir'].'/dependencies.txt');
    $middleware       = file_get_contents($container['templateDir'].'/middleware.txt');
    $routes           = file_get_contents($container['templateDir'].'/routes.txt');
    $settings         = file_get_contents($container['templateDir'].'/settings.txt');
    $gitignore        = file_get_contents($container['templateDir'].'/gitignore.txt');
    $composer         = file_get_contents($container['templateDir'].'/composer.txt');
    $phpunitxml       = file_get_contents($container['templateDir'].'/phpunitxml.txt');
    $phpunitbootstrap = file_get_contents($container['templateDir'].'/phpunitbootstrap.txt');

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
    return new SlimApi\Skeleton\SkeletonService($container['services.skeleton.structure']);
};

$container['commands.init'] = function($container) {
    return new SlimApi\Command\InitCommand($container['services.skeleton'], $container['services.database']);
};

$container['phinxApplication'] = function($container) {
    return new Phinx\Console\PhinxApplication;
};

$container['services.database'] = function($container) {
    return new SlimApi\Database\PhinxService($container['phinxApplication']);
};

$container['services.controller'] = function($container) {
    return 'SlimApi\Controller\ControllerService';
};

$container['services.controller.empty'] = function($container) {
    $indexAction     = file_get_contents($container['templateDir'].'/emptyIndexAction.txt');
    $getAction       = file_get_contents($container['templateDir'].'/emptyGetAction.txt');
    $postAction      = file_get_contents($container['templateDir'].'/emptyPostAction.txt');
    $putAction       = file_get_contents($container['templateDir'].'/emptyPutAction.txt');
    $deleteAction    = file_get_contents($container['templateDir'].'/emptyDeleteAction.txt');
    $controllerClass = file_get_contents($container['templateDir'].'/ControllerClass.txt');
    $service         = $container['services.controller'];
    return new $service($indexAction, $getAction, $postAction, $putAction, $deleteAction, $controllerClass, $container['namespace.root']);
};

$container['factory.generator'] = function($container) {
    return new SlimApi\Factory\GeneratorFactory([
        'model'      => new SlimApi\Generator\ModelGenerator($container['services.database'], $container['services.model']),
        'controller' => new SlimApi\Generator\ControllerGenerator($container['services.controller.empty']),
    ]);
};

$container['commands.generate'] = function($container) {
    return new SlimApi\Command\GenerateCommand($container['factory.generator']);
};

$container['commands'] = function ($container) {
    return [
        'init'     => $container['commands.init'],
        'generate' => $container['commands.generate'],
    ];
};

$container['application'] = function($container) {
    $application = new \Symfony\Component\Console\Application('SlimApi', '@package_version@');
    $application->addCommands($container['commands']);
    return $application;
};

return $container;
