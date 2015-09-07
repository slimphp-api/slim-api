<?php
namespace SlimApiTest\Controller;

use SlimApi\Controller\ControllerService;

class ControllerServiceTest extends \PHPUnit_Framework_TestCase
{
    use \SlimApiTest\DirectoryTrait;
    protected function setUp()
    {
        $controllerClass =<<<'EOL'
<?php
namespace $namespace\Controller;

class $nameController
{
$commands
}
EOL;
        $this->controllerService = new ControllerService('indexAction', 'getAction', 'postAction', 'putAction', 'deleteAction', $controllerClass, '', 'Project0');
    }

    public function testProcessCommand()
    {
        $this->controllerService->processCommand('addAction', 'index', 'post');
        $this->assertEquals(2, count($this->controllerService->commands));
        $this->assertEquals(['    indexAction', '    postAction'], $this->controllerService->commands);
    }

    public function testInvalidProcessCommand()
    {
        $this->setExpectedException('Exception', 'Invalid command type.');
        $this->controllerService->processCommand('subAction', 'index', 'post');
    }

    public function testEmptyCreate()
    {
        $this->setupDirectory();
        $expectClass =<<<'EOL'
<?php
namespace Project0\Controller;

class FooController
{

}
EOL;

        $this->controllerService->create('Foo');
        $this->assertTrue(is_file('src/Controller/FooController.php'));
        $this->assertEquals($expectClass, file_get_contents('src/Controller/FooController.php'));
    }

    public function testSingleCreate()
    {
        $this->setupDirectory();
        $expectClass =<<<'EOL'
<?php
namespace Project0\Controller;

class FooController
{
    indexAction
}
EOL;

        $this->controllerService->processCommand('addAction', 'index');
        $this->controllerService->create('Foo');
        $this->assertTrue(is_file('src/Controller/FooController.php'));
        $this->assertEquals($expectClass, file_get_contents('src/Controller/FooController.php'));
    }


    public function testMultipleCreate()
    {
        $this->setupDirectory();
        $expectClass =<<<'EOL'
<?php
namespace Project0\Controller;

class FooController
{
    indexAction

    postAction
}
EOL;

        $this->controllerService->processCommand('addAction', 'index', 'post');
        $this->controllerService->create('Foo');
        $this->assertTrue(is_file('src/Controller/FooController.php'));
        $this->assertEquals($expectClass, file_get_contents('src/Controller/FooController.php'));
    }

    public function testTargetLocation()
    {
        $this->assertEquals('src/Controller/FooController.php', $this->controllerService->targetLocation('Foo'));
    }
}
