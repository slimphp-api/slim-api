<?php
namespace SlimApiTest\Mock;

use SlimApi\Command\GenerateCommand;
use SlimApi\Generator\GeneratorInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ModelGeneratorMock implements GeneratorInterface
{
    public function __construct()
    {
    }

    public function validate($name, $fields)
    {
        return true;
    }

    public function process($name, $fields, $options)
    {
        return true;
    }
}
