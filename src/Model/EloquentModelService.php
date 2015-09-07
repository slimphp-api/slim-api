<?php
namespace SlimApi\Model;

use SlimApi\Interfaces\GeneratorServiceInterface;

class EloquentModelService implements ModelInterface, GeneratorServiceInterface
{
    public $commands = [];

    public function __construct($modelTemplate, $namespace)
    {
        $this->modelTemplate = $modelTemplate;
        $this->namespace     = $namespace;
    }

    public function processCommand($type, ...$arguments)
    {
        switch ($type) {
            case 'addColumn':
                $this->addColumn($arguments[0]);
                break;
            default:
                throw new \Exception('Invalid model command.');
                break;
        }
    }

    public function create($name)
    {
        $name     = ucfirst($name);
        $commands = '"'.implode('", "', $this->commands).'"';
        $content  = strtr($this->modelTemplate, ['$name' => $name, '$namespace' => $this->namespace, '$fields' => $commands]);
        return file_put_contents($this->targetLocation($name), $content);
    }

    public function targetLocation($name)
    {
        return 'src/Model/'.$name.'.php';
    }

    private function addColumn($name)
    {
        $this->commands[] = $name;
    }
}
