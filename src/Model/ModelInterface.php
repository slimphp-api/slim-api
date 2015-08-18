<?php
namespace SlimApi\Model;

interface ModelInterface
{
    public function __construct($modelTemplate, $namespace);
    public function create($name);
}
