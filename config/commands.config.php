<?php
$config = [];

$config['SlimApi\Command\InitCommand'] = function($container) {
    return new SlimApi\Command\InitCommand($container->get('SlimApi\Skeleton\SkeletonInterface'), $container->get('SlimApi\Database\DatabaseInterface'));
};

$config['SlimApi\Command\InitDbCommand'] = function($container) {
    return new SlimApi\Command\InitDbCommand($container->get('SlimApi\Database\DatabaseInterface'));
};

$config['SlimApi\Command\GenerateCommand'] = function($container) {
    return new SlimApi\Command\GenerateCommand($container->get('SlimApi\Factory\GeneratorFactory'));
};

$config['SlimApi\Command\RoutesCommand'] = function($container) {
    return new SlimApi\Command\RoutesCommand;
};

$config['commands'] = function ($container) {
    $commands = [];
    try {
        $commands['init:db']  = $container->get('SlimApi\Command\InitDbCommand');
        $commands['generate'] = $container->get('SlimApi\Command\GenerateCommand');
        $commands['routes']   = $container->get('SlimApi\Command\RoutesCommand');
    } catch (Slim\Exception\NotFoundException $e) {
        // ignore
        $commands = [];
    }
    $commands['init'] = $container->get('SlimApi\Command\InitCommand');
    return $commands;
};

return $config;
