<?php
$container = new Slim\Container((new SlimApi\Module)->loadDependencies());

foreach ($container->get('slim-api')['modules'] as $moduleNamespace) {
    $container->get($moduleNamespace.'\Init');
}

require 'application.php';
