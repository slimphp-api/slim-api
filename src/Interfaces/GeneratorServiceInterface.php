<?php
namespace SlimApi\Interfaces;

interface GeneratorServiceInterface
{
    public function processCommand($type, ...$arguments);
    public function create($name);
    public function targetLocation($name);
}
