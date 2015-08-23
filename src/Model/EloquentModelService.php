<?php
namespace SlimApi\Model;

use SlimApi\Interfaces\GeneratorServiceInterface;

class EloquentModelService implements ModelInterface, GeneratorServiceInterface
{
    public function __construct($modelTemplate, $namespace) {
        $this->modelTemplate = $modelTemplate;
        $this->namespace     = $namespace;
    }

    public function processCommand($type, ...$arguments)
    {

    }

    public function create($name)
    {
        $name    = ucfirst($name);
        $content = strtr($this->modelTemplate, ['$name' => $name, '$namespace' => $this->namespace]);
        return file_put_contents('src/Model/'.$name.'Model.php', $content);
    }

    public function targetLocation($name)
    {
        return 'src/Model/'.$name.'Model.php';
    }
}
