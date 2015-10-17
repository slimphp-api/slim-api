<?php
namespace SlimApi\Model;


interface ModelInterface
{
    /**
     * @param string $modelTemplate
     * @param string $namespace
     *
     * @return void
     */
    public function __construct($modelTemplate, $namespace);

    /**
     * Creates the model class
     *
     * @param string $name
     *
     * @return bool
     */
    public function create($name);
}
