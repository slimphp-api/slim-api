<?php
namespace SlimApi\Factory;

use SlimApi\Generator\GeneratorInterface;

class GeneratorFactory
{
    private $generators = [];

    /**
     * Fetches appropriate generator
     *
     * @param string $name
     *
     * @return GeneratorInterface|false the required generator or false if none.
     */
    public function fetch($name)
    {
        $generator = false;
        if (array_key_exists($name, $this->generators)) {
            $generator = $this->generators[$name];
        }
        return $generator;
    }

    /**
     * Add a generator to the factory
     *
     * @param string               $name   the name of the Generator
     * @param GeneratorInterface   $class  the generator object to return for the specified name
     */
    public function add($name, GeneratorInterface $class)
    {
        if (array_key_exists($name, $this->generators)) {
            throw new \InvalidArgumentException('Generator already exists.');
        }

        $this->generators[$name] = $class;
    }
}
