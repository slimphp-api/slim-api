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
        if (! array_key_exists('slim-api', $config)) {
            $config['slim-api'] = [
                'modules' => [
                    'SlimPhinx', //provides migrations
                    'SlimMvc' //provides structure
                ]
            ];
        } else {
            // load the target autoloader
            require 'vendor/autoload.php';
        }

        foreach ($config['slim-api']['modules'] as $moduleNamespace) {
            $config = array_merge($config, $moduleService->load($moduleNamespace));
        }

        return $config;
    }
}
