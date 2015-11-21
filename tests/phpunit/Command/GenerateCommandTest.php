<?php
namespace SlimApiTest\Command;

use SlimApi\Command\GenerateCommand;
// use SlimApiTest\Mock\ModelGeneratorMock;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use org\bovigo\vfs\vfsStream;

class MockModelGeneratorMock{public function validate() {return false;}}
class MockModelGeneratorMock2{public function validate() {return true;} public function process() {throw new \Exception;}}

class GenerateCommandTest extends \PHPUnit_Framework_TestCase
{
    use \SlimApiTest\DirectoryTrait;
    protected $tester;
    protected $command;

    protected function setUp()
    {
        $generatorFactory = $this->getMockBuilder('SlimApi\Factory\GeneratorFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $generatorFactory->method('fetch')
            ->willReturn(new MockModelGeneratorMock);

        $application = new Application('SlimApi', '@package_version@');
        $application->add(new GenerateCommand($generatorFactory));
        $this->command = $application->find('generate');
        $this->tester  = new CommandTester($this->command);
    }

    public function testNotEnoughArgs1()
    {
        $this->setExpectedException('Exception', 'Not enough arguments (missing: "name").');
        $this->tester->execute([
            'command' => $this->command->getName(),
            'type'    => '5foo'
        ]);
    }

    public function testNotEnoughArgs2()
    {
        $this->setExpectedException('Exception', 'Not enough arguments (missing: "type").');
        $this->tester->execute([
            'command' => $this->command->getName(),
            'name'    => 'bar'
        ]);
    }

    public function testInvalidType()
    {
        $this->setExpectedException('Exception', 'Invalid type.');
        $this->tester->execute([
            'command' => $this->command->getName(),
            'type'    => '5foo',
            'name'    => 'bar'
        ]);
    }

    public function testInvalidName()
    {
        $this->setExpectedException('Exception', 'Invalid name.');
        $this->tester->execute([
            'command' => $this->command->getName(),
            'type'    => 'scaffold',
            'name'    => '5bar'
        ]);
    }

    public function testInvalidFields()
    {
        $this->setExpectedException('Exception', 'Invalid fields.');
        $this->tester->execute([
            'command' => $this->command->getName(),
            'type'    => 'scaffold',
            'name'    => 'bar',
            'fields'  => 'some'
        ]);
    }

    public function testRunFromInvalidPath()
    {
        $this->setupDirectory();
        chdir('src/Model');
        $this->setExpectedException('Exception', 'Commands must be run from root of project.');
        $this->tester->execute([
            'command' => $this->command->getName(),
            'type'    => 'model',
            'name'    => 'bar'
        ]);
    }

    public function testModelExists()
    {
        $this->setupDirectory();
        $modelContent = '<?php namespace Project1\Model; class Test {}';
        file_put_contents('src/Model/Bar.php', $modelContent);

        $this->setExpectedException('Exception', 'Fields not valid.');
        $this->tester->execute([
            'command' => $this->command->getName(),
            'type'    => 'model',
            'name'    => 'bar'
        ]);
    }

    public function testProcessException()
    {
        $generatorFactory = $this->getMockBuilder('SlimApi\Factory\GeneratorFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $generatorFactory->method('fetch')
            ->willReturn(new MockModelGeneratorMock2);

        $application = new Application('SlimApi', '@package_version@');
        $application->add(new GenerateCommand($generatorFactory));
        $tester  = new CommandTester($application->find('generate'));

        $result = $tester->execute([
            'command' => $this->command->getName(),
            'type'    => 'model',
            'name'    => 'baz'
        ]);
        $this->assertEquals(0, $result);
    }

    public function testSuccess()
    {
        $generatorFactory = $this->getMockBuilder('SlimApi\Factory\GeneratorFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $generatorFactory->method('fetch')
            ->willReturn(new MockModelGeneratorMock2);

        $application = new Application('SlimApi', '@package_version@');
        $application->add(new GenerateCommand($generatorFactory));
        $this->command = $application->find('generate');
        $this->tester  = new CommandTester($this->command);

        $result = $this->tester->execute([
            'command' => $this->command->getName(),
            'type'    => 'model',
            'name'    => 'baz'
        ]);
        $this->assertEquals(0, $result);
    }
}
