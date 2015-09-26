<?php
namespace SlimApiTest\Generator;

use SlimApi\Controller\ControllerInterface;
use SlimApi\Generator\ControllerGenerator;
use SlimApi\Interfaces\GeneratorServiceInterface;

class RouteServiceMock implements GeneratorServiceInterface {public function processCommand($type, ...$arguments){} public function create($name){} public function targetLocation($name){}}
class ControllerServiceMock implements ControllerInterface {public $commands = []; public function create($name){} public function processCommand($type, $action){$this->commands[] = $action;} public function targetLocation($name){return 'src/Controller/'.$name.'Controller.php';}}
class DependencyServiceMock1 implements GeneratorServiceInterface {public function processCommand($type, ...$arguments){return true;} public function create($name){return true;} public function targetLocation($name){return 'src/Model/'.$name.'Model.php';}}

class ControllerGeneratorTest extends \PHPUnit_Framework_TestCase
{
    use \SlimApiTest\DirectoryTrait;
    protected function setUp()
    {
        $this->controllerGenerator = new ControllerGenerator(new ControllerServiceMock, new RouteServiceMock, new DependencyServiceMock1);
    }

    public function testInvalidAction()
    {
        $this->assertFalse($this->controllerGenerator->validate('Foo', ['bar']));
    }

    public function testValidAction()
    {
        $this->assertTrue($this->controllerGenerator->validate('Foo', ['index']));
    }

    public function testControllerExists()
    {
        $this->setupDirectory();
        $controllerContent = '<?php namespace Project0\Controller; class FooController {}';
        file_put_contents('src/Controller/FooController.php', $controllerContent);

        $this->assertFalse($this->controllerGenerator->validate('Foo', ['index']));
    }

    public function testProcessIndex()
    {
        $this->controllerGenerator->process('Foo', ['index']);
        $this->assertEquals(['index'], $this->controllerGenerator->controllerService->commands);
    }

    public function testProcessNone()
    {
        $this->controllerGenerator->process('Foo', []);
        $this->assertEquals(['index', 'get', 'post', 'put', 'delete'], $this->controllerGenerator->controllerService->commands);
    }
}
