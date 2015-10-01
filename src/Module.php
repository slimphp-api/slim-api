<?php
namespace SlimApi;

use SlimApi\Service\ConfigService;
use SlimApi\Service\ModuleService;

class Module
{
    /**
     * load the dependencies for the module.
     */
    public function loadDependencies()
    {
        // load the apis config
        $config = ConfigService::fetch(dirname(__DIR__));
        // load the app config
        $config = array_merge($config, ConfigService::fetch());

        $moduleService = new ModuleService;
        if (array_key_exists('slim-api', $config)) {
            foreach ($config['slim-api']['modules'] as $moduleNamespace) {
                $config = array_merge($config, $moduleService->load($moduleNamespace));
            }
        } else {
            $phinxModule = new \SlimPhinx\Module;
            $config = array_merge($config, $phinxModule->loadDependencies());
        }

        return $config;
    }
}
