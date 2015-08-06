<?php
namespace SlimApi\Service;

use Symfony\Component\Console\Input\ArrayInput;

class PhinxService
{
    private $application;

    public function __construct($phinxApp)
    {
        $this->application = $phinxApp;
    }

    private function run($command, $args = [])
    {
        $defaultArgs = [
            'command'   => $command
        ];
        $args  = array_merge($defaultArgs, $args);
        $input = new ArrayInput($args);

        $this->application->run($input);
    }

    public function init($directory)
    {
        $this->run('init', ['path' => $directory]);
    }
}
