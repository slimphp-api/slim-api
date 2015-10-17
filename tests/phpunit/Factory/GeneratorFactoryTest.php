<?php
namespace SlimApiTest\Factory;

use SlimApi\Factory\GeneratorFactory;
use SlimApiTest\Mock\ModelGeneratorMock;

class GeneratorFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->generatorFactory = new GeneratorFactory();
        $this->generatorFactory->add('model', new ModelGeneratorMock);
        $this->generatorFactory->add('controller', new ModelGeneratorMock);
        $this->generatorFactory->add('scaffold', new ModelGeneratorMock);
    }

    public function testReturnsModelGenerator()
    {
        $generator = $this->generatorFactory->fetch('model');
        $this->assertInstanceOf('SlimApi\Generator\GeneratorInterface', $generator);
    }

    public function testReturnsControllerGenerator()
    {
        $generator = $this->generatorFactory->fetch('controller');
        $this->assertInstanceOf('SlimApi\Generator\GeneratorInterface', $generator);
    }

    public function testReturnsScaffoldGenerator()
    {
        $generator = $this->generatorFactory->fetch('scaffold');
        $this->assertInstanceOf('SlimApi\Generator\GeneratorInterface', $generator);
    }
}
