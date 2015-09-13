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
    protected function setUp()
    {
        // $phinxApplicationMock = $this->getMockBuilder('stdClass')
        //     ->disableOriginalConstructor()
        //     ->getMock();
        // $phinxApplicationMock->method('run')
        //     ->willReturn(true);
        // $phinxApplicationMock->run();
        $this->modelGenerator = new ModelGenerator(new PhinxService(new PhinxApplication2Mock), new ModelServiceMock('', ''), new DependencyServiceMock);
    }

    public function testModelExists()
    {
        $this->modelGenerator = new ModelGenerator(new PhinxService(new PhinxApplicationMock), new ModelServiceMock('', ''), new DependencyServiceMock);
        $composerContent = '{"autoload":{"psr-4": {"Project1\\\": "src/"}}}';
        $modelContent = '<?php namespace Project1\Model; class Foo {}';
        chdir(__DIR__.'/../output');
        if (file_exists('project1')) {
            exec('rm -rf project1');
        }
        mkdir('project1');
        mkdir('project1/src');
        mkdir('project1/src/Model');
        file_put_contents('project1/composer.json', $composerContent);
        file_put_contents('project1/src/Model/FooModel.php', $modelContent);
        chdir('project1/');

        $this->assertFalse($this->modelGenerator->validate('Foo', []));
    }

    public function testModelNotExists()
    {
        $this->modelGenerator = new ModelGenerator(new PhinxService(new PhinxApplicationMock), new ModelServiceMock('', ''), new DependencyServiceMock);
        $composerContent = '{"autoload":{"psr-4": {"Project2\\\": "src/"}}}';
        chdir(__DIR__.'/../output');
        if (file_exists('project2')) {
            exec('rm -rf project2');
        }
        mkdir('project2');
        mkdir('project2/src');
        mkdir('project2/src/Model');
        file_put_contents('project2/composer.json', $composerContent);
        chdir('project2/');

        $this->assertTrue($this->modelGenerator->validate('Foo', []));
    }
}
