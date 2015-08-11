<?php
namespace SlimApi\Database;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class PhinxService implements DatabaseInterface
{
    private $application;

    private function run($command, $args = [])
    {
        $defaultArgs = ['command' => $command];
        $command     = $this->application->find($command);
        $args        = array_merge($defaultArgs, $args);
        $input       = new ArrayInput($args);
        $output      = new NullOutput();
        $command->run($input, $output);
    }

    public function __construct($phinxApp)
    {
        $this->application = $phinxApp;
    }

    public function init($directory)
    {
        $this->run('init', ['path' => $directory]);
    }

    public function create($name)
    {
    }

    public function addMigrationCommand($type, ...$arguments)
    {
    }
}
