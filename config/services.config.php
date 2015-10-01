<?php
$config = [];

$config['SlimApi\Service\RouteService'] = function($container) {
    return new SlimApi\Service\RouteService('src/routes.php', file_get_contents($container->get('templateDir').'/route.txt'), $container->get('namespace'));
};


return $config;
