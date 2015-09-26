<?php
namespace SlimApiTest\Command;

use SlimApi\Command\InitDbCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class InitDbCommandTest extends \PHPUnit_Framework_TestCase
{
    use \SlimApiTest\DirectoryTrait;

    protected $tester;
    protected $command;

    protected function setUp()
    {
        $phinxServiceMock = $this->getMockBuilder('SlimApi\Database\DatabaseInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $phinxServiceMock->method('init');

        $application = new Application('SlimApi', '@package_version@');
        $application->add(new InitDbCommand($phinxServiceMock));
        $this->command = $application->find('init:db');
        $this->tester  = new CommandTester($this->command);
    }

    public function testMustComposer()
    {
        $this->setupDirectory();
        chdir('src');
        $this->setExpectedException('Exception', 'Commands must be run from root of project.');
        $this->tester->execute(['command' => $this->command->getName()]);
    }

    public function testInit()
    {
        $this->setupDirectory();
        $this->tester->execute(['command' => $this->command->getName()]);
        $this->assertEquals(InitDbCommand::$successMessage, trim($this->tester->getDisplay()));
    }

    public function testExecuteException()
    {
        $this->command->databaseService->method('init')->will($this->throwException(new \Exception('Foo exception')));

        $this->setupDirectory();
        $this->tester->execute(['command' => $this->command->getName()]);
        $this->assertEquals('Foo exception'.PHP_EOL, $this->tester->getDisplay());
    }
}
