<?php
namespace SlimApiTest\Command;

use SlimApi\Command\Generate\GenerateModelCommand;
use SlimApiTest\Mock\ModelGeneratorMock;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use org\bovigo\vfs\vfsStream;

class MockModelGeneratorMock implements \SlimApi\Generator\GeneratorInterface {public function validate($name, $fields) {return false;} public function process($name, $fields) {}}
class MockModelGeneratorMock2{public function validate() {return true;} public function process() {throw new \Exception;}}

class GenerateModelCommandTest extends \PHPUnit_Framework_TestCase
{
    use \SlimApiTest\DirectoryTrait;

    protected $tester;
    protected $command;

    protected function setUp()
    {
        $application = new Application('SlimApi', '@package_version@');
        $application->add(new GenerateModelCommand(new ModelGeneratorMock));
        $this->command = $application->find('generate:model');
        $this->tester  = new CommandTester($this->command);
    }

    public function testNotEnoughArgs1()
    {
        $this->setExpectedException('Exception', 'Not enough arguments.');
        $this->tester->execute([
            'command' => $this->command->getName(),
        ]);
    }

    public function testInvalidName()
    {
        $output = $this->tester->execute([
            'command' => $this->command->getName(),
            'name'    => '5bar'
        ]);
        $this->assertEquals(1, $output);
        $this->assertRegExp('/Invalid name./', $this->tester->getDisplay());
    }

    public function testInvalidFields()
    {
        $output = $this->tester->execute([
            'command' => $this->command->getName(),
            'name'    => 'bar',
            'fields'  => 'some'
        ]);
        $this->assertEquals(1, $output);
        $this->assertRegExp('/Invalid fields./', $this->tester->getDisplay());
    }

    public function testRunFromInvalidPath()
    {
        $this->setupDirectory();
        chdir(__DIR__);
        $output = $this->tester->execute([
            'command' => $this->command->getName(),
            'name'    => 'bar'
        ]);
        $this->assertEquals(1, $output);
        $this->assertRegExp('/Commands must be run from root of project./', $this->tester->getDisplay());
    }

    public function testProcessException()
    {
        $application = new Application('SlimApi', '@package_version@');
        $application->add(new GenerateModelCommand(new ModelGeneratorMock));
        $tester  = new CommandTester($application->find('generate:model'));

        $result = $tester->execute([
            'command' => $this->command->getName(),
            'name'    => 'baz'
        ]);
        $this->assertEquals(0, $result);
    }

    public function testSuccess()
    {
        $result = $this->tester->execute([
            'command' => $this->command->getName(),
            'name'    => 'baz'
        ]);
        $this->assertEquals(0, $result);
    }
}
