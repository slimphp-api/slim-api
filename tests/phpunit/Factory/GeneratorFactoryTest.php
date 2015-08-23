<?php
namespace SlimApiTest\Factory;

use SlimApi\Factory\GeneratorFactory;
use SlimApiTest\Mock\ModelGeneratorMock;

class GeneratorFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->generatorFactory = new GeneratorFactory(['model' => new ModelGeneratorMock]);
    }

    public function testReturnsModelGenerator()
    {
        $generator = $this->generatorFactory->fetch('model');
        $this->assertInstanceOf('SlimApi\Generator\GeneratorInterface', $generator);
    }
}
