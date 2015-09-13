<?php
namespace SlimApi\Service;

class ModuleService
{
    public function __construct($container)
    {
        $this->container = $container;
    }

    public function load($moduleNamespace)
    {
        // load the target autoloader
        require 'vendor/autoload.php';
        // load the modules dependency file
        $moduleClass = '\\'.$moduleNamespace.'\\Module';
        $module = new $moduleClass;
        $module->loadDependencies($this->container);
    }
}
