<?php
namespace SlimApiTest\Generator;

use SlimApi\Database\DatabaseInterface;
use SlimApi\Generator\ModelGenerator;
use SlimApi\Interfaces\GeneratorServiceInterface;
use SlimApi\Model\ModelInterface;
use org\bovigo\vfs\vfsStream;

class PhinxService implements DatabaseInterface, GeneratorServiceInterface {
    public $commands = [];
    public function init($directory){}
    public function processCommand($type, ...$arguments){$this->commands[] = $type;}
    public function create($name){}
    public function targetLocation($name){}
}
class PhinxApplicationMock {public function run(){return true;} public function find($name) { $class = '\Phinx\Console\Command\\'.ucfirst($name); return new $class; }}
class CommandMock {public function run(){return 0;}}
class ModelServiceMock implements ModelInterface {public function processCommand($type, ...$arguments){return true;} public function create($name){return true;} public function __construct($modelTemplate, $namespace){} public function targetLocation($name){return 'src/Model/'.$name.'Model.php';}}
class PhinxApplication2Mock {public function run(){return true;} public function find($name) { return new CommandMock; }}
class DependencyServiceMock implements GeneratorServiceInterface {public function processCommand($type, ...$arguments){return true;} public function create($name){return true;} public function targetLocation($name){return 'src/Model/'.$name.'Model.php';}}

class ModelGeneratorTest extends \PHPUnit_Framework_TestCase
{
    use \SlimApiTest\DirectoryTrait;
    protected function setUp()
    {
        $this->modelGenerator = new ModelGenerator(new PhinxService(new PhinxApplication2Mock), new ModelServiceMock('', ''), new DependencyServiceMock);
    }

    public function testModelExists()
    {
        $this->modelGenerator = new ModelGenerator(new PhinxService(new PhinxApplicationMock), new ModelServiceMock('', ''), new DependencyServiceMock);
        $this->setupDirectory();
        $modelContent = '<?php namespace Project0\Model; class Foo {}';
        file_put_contents('src/Model/FooModel.php', $modelContent);
        $this->assertFalse($this->modelGenerator->validate('Foo', []));
    }

    public function testModelNotExists()
    {
        $this->modelGenerator = new ModelGenerator(new PhinxService(new PhinxApplicationMock), new ModelServiceMock('', ''), new DependencyServiceMock);
        $this->setupDirectory();
        $this->assertTrue($this->modelGenerator->validate('Foo', []));
    }

    public function testEmpty()
    {
        $this->assertEquals($this->modelGenerator->migrationService->commands, []);
    }

    public function testSimpleProcess()
    {
        $this->modelGenerator->process('foo', []);
        $this->assertEquals($this->modelGenerator->migrationService->commands, ['create', 'finalise']);
    }

    public function testProcess()
    {
        $this->modelGenerator->process('foo', ['id:integer']);
        $this->assertEquals($this->modelGenerator->migrationService->commands, ['create', 'addColumn', 'finalise']);
    }
}
