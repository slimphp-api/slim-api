<?php
$config = [];

$config['SlimApi\Service\RouteService'] = function($container) {
    return new SlimApi\Service\RouteService('src/routes.php', file_get_contents($container->get('templateDir').'/route.txt'), $container->get('namespace'));
};

$config['SlimApi\Factory\GeneratorFactory'] = function($container) {
    return new SlimApi\Factory\GeneratorFactory();
};

$config['SlimApi\Skeleton\SkeletonInterface'] = function($container) {
    return new SlimApi\Skeleton\SkeletonService($container->get('services.skeleton.structure'));
};

return $config;
