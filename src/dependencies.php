<?php
$container = new Pimple\Container;

$container['namespace.root'] = function($container) {
    // alternativly load composer.json?
    return ucfirst(basename(getcwd()));
};

$container['model.structure'] = function($container) {
    return <<<'EOT'
<?php
namespace $namespace\Model;

use \Illuminate\Database\Eloquent\Model;

class $name extends Model
{
}
EOT;
};

$container['services.model'] = function($container) {
    return new \SlimApi\Model\EloquentModelService($container['model.structure'], $container['namespace.root']);
};

$container['services.skeleton.structure'] = function($container) {
    // I thought long and hard about to decalre dependencies
    // should there be one for each type? controllers, services, models?
    // in the end it's not going to be managed by the api generators,
    // so a generic starting place for people to begin with seemed sensible
    $dependencies = <<<'EOT'
<?php
// DIC configuration

$container = $app->getContainer();

// -----------------------------------------------------------------------------
// Service providers
// -----------------------------------------------------------------------------

// Flash messages
// $container->register(new \Slim\Flash\Messages);

// -----------------------------------------------------------------------------
// Service factories
// -----------------------------------------------------------------------------

// monolog
// $container['logger'] = function ($c) {
//     $settings = $c['settings']['logger'];
//     $logger = new \Monolog\Logger($settings['name']);
//     $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
//     $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], \Monolog\Logger::DEBUG));
//     return $logger;
// };

// -----------------------------------------------------------------------------
// Controller factories
// -----------------------------------------------------------------------------
EOT;

    $middleware = <<<'EOT'
<?php
// Application middleware
// e.g: $app->add(new \gabriel403\SlimJson);
EOT;

    $routes = <<<'EOT'
<?php
// Routes
EOT;

    $settings = <<<'EOT'
<?php
$config = [];
$files  = glob('config/*.config.php', GLOB_BRACE);
foreach ($files as $file) {
    $config = array_merge($config, (require $file));
}
return $config;
EOT;

    $gitignore = <<<'EOT'
composer.phar
vendor/

# Commit your application's lock file http://getcomposer.org/doc/01-basic-usage.md#composer-lock-the-lock-file
# You may choose to ignore a library lock file http://getcomposer.org/doc/02-libraries.md#lock-file
# composer.lock
EOT;

    $composer = <<<'EOT'
{
    "require": {
        "php": "^5.6",
        "gabriel403/slim-api": "*@beta",
        "slim/slim": "3.*@beta",
        "robmorgan/phinx": "0.*",
        "illuminate/database": "5.*"
    },
    "require-dev": {
        "phpunit/phpunit": "^5.0@dev"
    },
    "autoload": {
        "psr-4": {"$name\\": "src/"}
    },
    "autoload-dev": {
        "psr-4": {"$nameTest\\": "tests/phpunit/"}
    }
}
EOT;

    $phpunitxml = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>

<phpunit backupGlobals="false"
         backupStaticAttributes="false"
         colors="true"
         convertErrorsToExceptions="true"
         convertNoticesToExceptions="true"
         convertWarningsToExceptions="true"
         processIsolation="false"
         stopOnFailure="false"
         syntaxCheck="false"
         bootstrap="tests/phpunit/bootstrap.php"
>
    <testsuites>
        <testsuite name="App Test Suite">
            <directory>./tests/phpunit/</directory>
        </testsuite>
    </testsuites>

    <logging>
        <log type="coverage-html" target="build/coverage"/>
        <log type="coverage-clover" target="build/logs/clover.xml"/>
    </logging>

    <filter>
        <whitelist>
            <directory>./src/</directory>
        </whitelist>
    </filter>
</phpunit>
EOT;

    $phpunitbootstrap = <<<'EOT'
<?php
require __DIR__.'/../../vendor/autoload.php';
EOT;

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
    return new SlimApi\Service\SkeletonService($container['services.skeleton.structure']);
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

$container['factory.generator'] = function($container) {
    return new SlimApi\Factory\GeneratorFactory(['model' => new SlimApi\Generator\ModelGenerator($container['services.database'], $container['services.model'])]);
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
