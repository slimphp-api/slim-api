<?php
namespace SlimApi\Factory;

class GeneratorFactory
{
    public function __construct($generators)
    {
        $this->generators = $generators;
    }

    public function fetch($type)
    {
        $generator = false;
        switch ($type) {
            case 'model':
                $generator = $this->generators['model'];
                break;
            case 'controller':
                $generator = $this->generators['controller'];
                break;
        }
        return $generator;
    }
}
