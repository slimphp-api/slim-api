<?php
namespace SlimApi\Service;

class ConfigService
{
    /**
     * Fetches configuration from the running app
     *
     * @return array
     */
    public static function fetch()
    {
        $config = [];
        $files  = glob('config/*.config.php', GLOB_BRACE);
        foreach ($files as $file) {
            $config = array_merge($config, (require $file));
        }
        return $config;
    }
}
