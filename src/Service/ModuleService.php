<?php
namespace SlimApi\Service;

class ModuleService
{
    /**
     * Load the external modules Module class and dependencies
     *
     * @param string $moduleNamespace
     *
     * @return
     */
    public function load($moduleNamespace)
    {
        // load the modules dependency file
        $moduleClass = '\\'.$moduleNamespace.'\\Module';
        $module = new $moduleClass;
        return $module->loadDependencies();
    }
}
