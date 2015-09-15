<?php
namespace SlimApi\Interfaces;

interface GeneratorServiceInterface
{
    /**
     * Process the individual commands
     *
     * @param string $type
     * @param mixed $arguments
     *
     * @return void
     */
    public function processCommand($type, ...$arguments);

    /**
     * Create the controller based on the commands and output to file
     *
     * @param string $name
     *
     * @return bool|int return status
     */
    public function create($name);

    /**
     * Defines the file location
     *
     * @param string $name
     *
     * @return string the file location
     */
    public function targetLocation($name);
}
