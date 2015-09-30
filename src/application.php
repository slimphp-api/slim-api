<?php
$application = new \Symfony\Component\Console\Application('SlimApi', '@package_version@');
$application->addCommands($container->get('commands'));
$application->run();
