<?php
namespace SlimApi\Generator;

interface GeneratorInterface
{
    public function validate($name, $fields);
    public function process($name, $fields);
}
