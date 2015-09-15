<?php
namespace SlimApi\Factory;

class GeneratorFactory
{
    /**
     * @param array $generators
     *
     * @return
     */
    public function __construct($generators)
    {
        $this->generators = $generators;
    }

    /**
     * Fetches appropriate generator
     *
     * @param string $type
     *
     * @return GeneratorInterface|false the required generator or false if none.
     */
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
            case 'scaffold':
                $generator = $this->generators['scaffold'];
                break;
        }
        return $generator;
    }
}
