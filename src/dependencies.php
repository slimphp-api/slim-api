<?php
$container = new Pimple\Container;

$container['commands'] = function ($container) {
    return [
    ];
};

$container['application'] = function($container) {
    $application = new \Symfony\Component\Console\Application('SlimApi', '@package_version@');
    $application->addCommands($container['commands']);
    return $application;
};

return $container;
