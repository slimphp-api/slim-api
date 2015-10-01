<?php
$container = new Slim\Container((new SlimApi\Module)->loadDependencies());
require 'application.php';
