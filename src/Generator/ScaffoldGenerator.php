<?php
namespace SlimApi\Generator;

class ScaffoldGenerator implements GeneratorInterface
{
    public function __construct(GeneratorInterface $controllerGenerator, GeneratorInterface $modelGenerator)
    {
        $this->controllerGenerator = $controllerGenerator;
        $this->modelGenerator      = $modelGenerator;
    }

    public function validate($name, $fields)
    {
        // fields are ignored in scaffolding, everything's included but models still have fields
        return ($this->modelGenerator->validate($name, $fields) && $this->controllerGenerator->validate($name, []));
    }

    public function process($name, $fields)
    {
        $this->modelGenerator->process($name, $fields);
        $this->controllerGenerator->process($name, []);
    }
}
