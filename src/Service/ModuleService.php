<?php
namespace SlimApi\Service;

class ModuleService
{
    /**
     * @param mixed $container
     *
     * @return
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Load the external modules Module class and dependencies
     *
     * @param string $moduleNamespace
     *
     * @return
     */
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
