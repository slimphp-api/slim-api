<?php
namespace SlimApiTest\Generator;

use SlimApi\Database\PhinxService;
use SlimApi\Generator\ModelGenerator;
use SlimApi\Model\ModelInterface;
use org\bovigo\vfs\vfsStream;

class PhinxApplicationMock {public function run(){return true;} public function find($name) { $class = '\Phinx\Console\Command\\'.ucfirst($name); return new $class; }}
class CommandMock {public function run(){return 0;}}
class ModelServiceMock implements ModelInterface {public function create($name){return true;} public function __construct($modelTemplate, $namespace){} public function targetLocation($name){return 'src/Model/'.$name.'Model.php';}}
class PhinxApplication2Mock {public function run(){return true;} public function find($name) { return new CommandMock; }}

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
        $this->modelGenerator = new ModelGenerator(new PhinxService(new PhinxApplication2Mock), new ModelServiceMock('', ''));
    }

    public function testModelExists()
    {
        $this->modelGenerator = new ModelGenerator(new PhinxService(new PhinxApplicationMock), new ModelServiceMock('', ''));
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
        $this->modelGenerator = new ModelGenerator(new PhinxService(new PhinxApplicationMock), new ModelServiceMock('', ''));
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

    public function testSimpleProcess()
    {
        $expected = ['$table = $this->table("Foo");', '$table->addColumn("col1", "integer");', '$table->create();'];
        $this->modelGenerator->process('Foo', ['col1:integer']);
        $this->assertEquals($expected, $this->modelGenerator->migrationService->commands);
    }

    public function testLimitProcess()
    {
        $expected = ['$table = $this->table("Foo");', '$table->addColumn("col1", "integer", ["limit" => 30]);', '$table->create();'];
        $this->modelGenerator->process('Foo', ['col1:integer:30']);
        $this->assertEquals($expected, $this->modelGenerator->migrationService->commands);
    }

    public function testNullableProcess()
    {
        $expected = ['$table = $this->table("Foo");', '$table->addColumn("col1", "integer", ["null" => false]);', '$table->create();'];
        $this->modelGenerator->process('Foo', ['col1:integer::false']);
        $this->assertEquals($expected, $this->modelGenerator->migrationService->commands);
    }

    public function testUniqueProcess()
    {
        $expected = ['$table = $this->table("Foo");', '$table->addColumn("col1", "integer", ["unique" => true]);', '$table->create();'];
        $this->modelGenerator->process('Foo', ['col1:integer:::true']);
        $this->assertEquals($expected, $this->modelGenerator->migrationService->commands);
    }

    public function testLimitUniqueProcess()
    {
        $expected = ['$table = $this->table("Foo");', '$table->addColumn("col1", "integer", ["limit" => 30, "unique" => true]);', '$table->create();'];
        $this->modelGenerator->process('Foo', ['col1:integer:30::true']);
        $this->assertEquals($expected, $this->modelGenerator->migrationService->commands);
    }

    public function testAllProcess()
    {
        $expected = ['$table = $this->table("Foo");', '$table->addColumn("col1", "integer", ["limit" => 30, "null" => false, "unique" => true]);', '$table->create();'];
        $this->modelGenerator->process('Foo', ['col1:integer:30:false:true']);
        $this->assertEquals($expected, $this->modelGenerator->migrationService->commands);
    }
}
