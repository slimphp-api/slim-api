<?php
$container = new Slim\Container(SlimApi\Service\ConfigService::fetch());

$container['templateDir'] = function($container) {
    return realpath(__DIR__.'/../templates');
};

$container['services.skeleton.structure'] = function($container) {
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
        'config' => ['slim-api.config.php' => $apiConfig],
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

$container['SlimApi\Skeleton\SkeletonInterface'] = function($container) {
    return new SlimApi\Skeleton\SkeletonService($container->get('services.skeleton.structure'));
};

$container['SlimApi\Command\InitCommand'] = function($container) {
    if (!$container->has('SlimApi\Database\DatabaseInterface')) {
        $phinxModule = new \SlimPhinx\Module;
        $phinxModule->loadDependencies($container);
    }
    return new SlimApi\Command\InitCommand($container->get('SlimApi\Skeleton\SkeletonInterface'), $container->get('SlimApi\Database\DatabaseInterface'));
};

$container['SlimApi\Command\InitDbCommand'] = function($container) {
    return new SlimApi\Command\InitDbCommand($container->get('SlimApi\Database\DatabaseInterface'));
};

$container['SlimApi\Controller\ControllerInterface'] = function($container) {
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
    $service         = $container->get('SlimApi\Controller\ControllerInterface');
    return new $service($indexAction, $getAction, $postAction, $putAction, $deleteAction, $controllerClass, $controllerCons, $container->get('namespace'));
};

$container['services.controller.empty'] = function($container) {
    $indexAction     = file_get_contents($container->get('templateDir').'/emptyIndexAction.txt');
    $getAction       = file_get_contents($container->get('templateDir').'/emptyGetAction.txt');
    $postAction      = file_get_contents($container->get('templateDir').'/emptyPostAction.txt');
    $putAction       = file_get_contents($container->get('templateDir').'/emptyPutAction.txt');
    $deleteAction    = file_get_contents($container->get('templateDir').'/emptyDeleteAction.txt');
    $controllerClass = file_get_contents($container->get('templateDir').'/ControllerClass.txt');
    $service         = $container->get('SlimApi\Controller\ControllerInterface');
    return new $service($indexAction, $getAction, $postAction, $putAction, $deleteAction, $controllerClass, '', $container->get('namespace'));
};

$container['SlimApi\Service\RouteService'] = function($container) {
    return new SlimApi\Service\RouteService('src/routes.php', file_get_contents($container->get('templateDir').'/route.txt'), $container->get('namespace'));
};

$container['SlimApi\Service\DependencyService'] = function($container) {
    return new SlimApi\Service\DependencyService('src/dependencies.php', file_get_contents($container->get('templateDir').'/ControllerDependency.txt'), file_get_contents($container->get('templateDir').'/ModelDependency.txt'), $container->get('namespace'));
};

$container['SlimApi\Generator\ModelGenerator'] = function($container) {
    return new SlimApi\Generator\ModelGenerator($container->get('SlimApi\Database\DatabaseInterface'), $container->get('SlimApi\Model\ModelInterface'), $container->get('SlimApi\Service\DependencyService'));
};

$container['factory.generator.controller.empty'] = function($container) {
    return new SlimApi\Generator\ControllerGenerator($container->get('services.controller.empty'), $container->get('SlimApi\Service\RouteService'), $container->get('SlimApi\Service\DependencyService'));
};

$container['factory.generator.controller.populated'] = function($container) {
    return new SlimApi\Generator\ControllerGenerator($container->get('services.controller.populated'), $container->get('SlimApi\Service\RouteService'), $container->get('SlimApi\Service\DependencyService'));
};

$container['SlimApi\Generator\ScaffoldGenerator'] = function($container) {
    return new SlimApi\Generator\ScaffoldGenerator($container->get('factory.generator.controller.populated'), $container->get('SlimApi\Generator\ModelGenerator'));
};

$container['SlimApi\Command\Generate\GenerateControllerCommand'] = function($container) {
    return new SlimApi\Command\Generate\GenerateControllerCommand($container->get('factory.generator.controller.empty'));
};

$container['SlimApi\Command\Generate\GenerateModelCommand'] = function($container) {
    return new SlimApi\Command\Generate\GenerateModelCommand($container->get('SlimApi\Generator\ModelGenerator'));
};

$container['SlimApi\Command\Generate\GenerateScaffoldCommand'] = function($container) {
    return new SlimApi\Command\Generate\GenerateScaffoldCommand($container->get('SlimApi\Generator\ScaffoldGenerator'));
};

$container['SlimApi\Command\RoutesCommand'] = function($container) {
    return new SlimApi\Command\RoutesCommand;
};

$container['SlimApi\Service\ModuleService'] = function($container) {
    return new SlimApi\Service\ModuleService($container);
};

$container['api.config'] = function($container) {
    $config = SlimApi\Service\ConfigService::fetch();
    if (!array_key_exists('slim-api', $config) ||
        !array_key_exists('modules', $config['slim-api'])
    ) {
        throw new UnexpectedValueException("Invalid configuration.");
    }
    $moduleService = $container->get('SlimApi\Service\ModuleService');
    foreach ($config['slim-api']['modules'] as $moduleNamespace) {
        $moduleService->load($moduleNamespace);
    }
};

$container['commands'] = function ($container) {
    $commands = [];
    try {
        $config                          = $container->get('api.config');
        $commands['init :db']            = $container->get('SlimApi\Command\InitDbCommand');
        $commands['generate:controller'] = $container->get('SlimApi\Command\Generate\GenerateControllerCommand');
        $commands['generate:model']      = $container->get('SlimApi\Command\Generate\GenerateModelCommand');
        $commands['generate:scaffold']   = $container->get('SlimApi\Command\Generate\GenerateScaffoldCommand');
        $commands['routes']              = $container->get('SlimApi\Command\RoutesCommand');
    } catch (UnexpectedValueException $e) {
        // ignore
        $commands = [];
    }
    $commands['init'] = $container->get('SlimApi\Command\InitCommand');
    return $commands;
};

$container['application'] = function($container) {
    $application = new \Symfony\Component\Console\Application('SlimApi', '@package_version@');
    $application->addCommands($container->get('commands'));
    return $application;
};

return $container;
