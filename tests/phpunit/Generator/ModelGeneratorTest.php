<?php
namespace SlimApiTest\Generator;

use SlimApi\Database\DatabaseInterface;
use SlimApi\Generator\ModelGenerator;
use SlimApi\Interfaces\GeneratorServiceInterface;
use SlimApi\Model\ModelInterface;
use org\bovigo\vfs\vfsStream;

class PhinxService implements DatabaseInterface, GeneratorServiceInterface {public function init($directory){} public function processCommand($type, ...$arguments){} public function create($name){} public function targetLocation($name){}}
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
        $this->modelGenerator = new ModelGenerator(new PhinxService(new PhinxApplicationMock), new ModelServiceMock('', ''), new DependencyServiceMock);
        $this->setupDirectory();
    }

    public function testModelExists()
    {
        $modelContent = '<?php namespace Project0\Model; class Foo {}';
        file_put_contents('src/Model/FooModel.php', $modelContent);

        $this->assertFalse($this->modelGenerator->validate('Foo', []));
    }

    public function testModelNotExists()
    {
        $this->assertTrue($this->modelGenerator->validate('Foo', []));
    }
}
