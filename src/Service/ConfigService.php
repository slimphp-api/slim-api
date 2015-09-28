<?php
namespace SlimApi\Service;

class ConfigService
{
    /**
     * Fetches configuration from the running app
     *
     * @return array
     */
    public static function fetch($dir = false)
    {
        if (false === $dir) {
            $dir = getcwd();
        }
        $config = [];
        $files  = glob($dir.'/config/*.config.php', GLOB_BRACE);
        foreach ($files as $file) {
            $config = array_merge($config, (require $file));
        }
        return $config;
    }
}
