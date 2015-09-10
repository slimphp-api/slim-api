<?php
namespace SlimApi\Database;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use SlimApi\Interfaces\GeneratorServiceInterface;

class PhinxService implements DatabaseInterface, GeneratorServiceInterface
{
    public $commands = [];
    private $application;
    private $types = ['string', 'text', 'integer', 'biginteger', 'float', 'decimal', 'datetime', 'timestamp', 'time', 'date', 'binary', 'boolean'];

    public function __construct($phinxApp)
    {
        $this->application = $phinxApp;
    }

    public function init($directory)
    {
        $this->run('init', ['path' => $directory]);
    }

    public function processCommand($type, ...$arguments)
    {
        switch ($type) {
            case 'create':
                $this->createTable(strtolower($arguments[0]));
                break;
            case 'addColumn':
                $arguments = array_pad($arguments, 5, null);
                $this->addColumn(...$arguments);
                break;
            case 'finalise':
                $this->finalise();
                break;
            default:
                throw new \Exception('Invalid migration command.');
                break;
        }
    }

    public function create($name)
    {
        PhinxMigration::$commands = $this->commands;
        $this->run('create', ['command' => 'create', 'name' => $name, '--class' => 'SlimApi\Database\PhinxMigration']);
    }

    public function targetLocation($name)
    {
        return '';
    }

    private function createTable($name)
    {
        $command          = '$table = $this->table("$name");';
        $command          = strtr($command, ['$name' => $name]);
        $this->commands[] = $command;
    }

    private function run($command, $args = [])
    {
        $defaultArgs = [];
        $command     = $this->application->find($command);
        $args        = array_merge($defaultArgs, $args);
        $input       = new ArrayInput($args);
        $output      = new NullOutput();
        $command->run($input, $output);
    }

    private function addColumn($name, $type, $limit, $nullable, $unique)
    {
        if (!in_array($type, $this->types)) {
            throw new \Exception("Type not valid.");
        }

        $extras           = [];
        $extrasStrPreFix  = ", [";
        $extrasStrPostFix = "]";
        $extrasStr        = "";

        if (!is_null($limit)) {
            $extras[] = sprintf('"limit" => %d', $limit);
        }

        if ('false' === $nullable || 'true' === $nullable) {
            $extras[] = sprintf('"null" => %s', $nullable);
        }

        if ('false' === $unique || 'true' === $unique) {
            $extras[] = sprintf('"unique" => %s', $unique);
        }

        if (count($extras) > 0) {
            $extrasStr = $extrasStrPreFix.implode(", ", $extras).$extrasStrPostFix;
        }

        $command          = sprintf('$table->addColumn("%s", "%s"%s);', $name, $type, $extrasStr);
        $this->commands[] = $command;
    }

    private function finalise()
    {
        $command          = '$table->create();';
        $this->commands[] = $command;
    }
}
