<?php
namespace SlimApi\Model;

class EloquentModelService implements ModelInterface
{
    public function __construct($modelTemplate, $namespace) {
        $this->modelTemplate = $modelTemplate;
        $this->namespace     = $namespace;
    }

    public function create($name)
    {
        $name    = ucfirst($name);
        $content = strtr($this->modelTemplate, ['$name' => $name, '$namespace' => $this->namespace]);

        return file_put_contents('src/Model/'.$name.'.php', $content);
    }
}
