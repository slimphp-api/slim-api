<?php
namespace SlimApi\Service;

use Symfony\Component\Console\Input\ArrayInput;

class ComposerService
{
    private $application;

    public function __construct($composerApp)
    {
        $this->application = $composerApp;
    }

    private function run($command, $directory, $args)
    {
        $defaultArgs = [
            'command'   => $command,
            'directory' => $directory
        ];
        $args  = array_merge($defaultArgs, $args);
        $input = new ArrayInput($args);

        $this->application->run($input);
    }

    public function create($directory)
    {
        $this->run('create-project', $directory, ['-n' => false, '-s' => 'dev', 'package' => 'gabriel403/slim-api-skeleton']);
    }
}
