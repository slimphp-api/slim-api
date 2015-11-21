<?php
namespace SlimApiTest\Command;

use SlimApi\Command\InitCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class InitCommandTest extends \PHPUnit_Framework_TestCase
{
    protected $tester;
    protected $command;

    protected function setUp()
    {
        $composerServiceMock = $this->getMockBuilder('SlimApi\Skeleton\SkeletonService')
            ->disableOriginalConstructor()
            ->getMock();
        $composerServiceMock->method('create');

        $phinxServiceMock = $this->getMockBuilder('SlimApi\Migration\MigrationInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $phinxServiceMock->method('init');

        $application = new Application('SlimApi', '@package_version@');
        $application->add(new InitCommand($composerServiceMock, $phinxServiceMock));
        $this->command = $application->find('init');
        $this->tester  = new CommandTester($this->command);
    }

    public function testNameMissing()
    {
        $this->setExpectedException('Exception', 'Not enough arguments (missing: "name").');
        $this->tester->execute([
            'command' => $this->command->getName(),
        ]);
    }

    public function testInvalidName1()
    {
        $this->setExpectedException('Exception', 'Invalid name.');
        $this->tester->execute([
            'command' => $this->command->getName(),
            'name'    => 'Te st',
        ]);
    }

    public function testInvalidName2()
    {
        $this->setExpectedException('Exception', 'Invalid name.');
        $this->tester->execute([
            'command' => $this->command->getName(),
            'name'    => 'Te-st',
        ]);
    }

    public function testValidName1()
    {
        $this->tester->execute([
            'command'  => $this->command->getName(),
            'name'     => 'Test',
            'location' => 'foo',
        ]);
    }

    public function testValidName2()
    {
        $this->tester->execute([
            'command'  => $this->command->getName(),
            'name'     => 'TeSt',
            'location' => 'foo',
        ]);
    }

    public function testValidName3()
    {
        $this->tester->execute([
            'command'  => $this->command->getName(),
            'name'     => 'test',
            'location' => 'foo',
        ]);
    }

    public function testComposerNotExecutable()
    {
        $this->setExpectedException('Exception');
        $this->tester->execute([
            'command'             => $this->command->getName(),
            'name'                => 'butts',
            '--composer-location' => '/etc/composer',
        ]);
    }

    public function testComposerRelativeExecutable()
    {
        $this->setExpectedException('Exception');
        $this->tester->execute([
            'command'             => $this->command->getName(),
            'name'                => 'butts',
            '--composer-location' => 'composer',
        ]);
    }

    public function testNotEmpty()
    {
        $this->setExpectedException('Exception', 'Path not empty.');
        $this->tester->execute([
            'command'  => $this->command->getName(),
            'name'     => 'Butts',
        ]);
    }

    public function testNotWritable()
    {
        $this->setExpectedException('Exception', 'Cannot write to path.');
        $this->tester->execute([
            'command'  => $this->command->getName(),
            'name'     => 'Butts',
            'location' => '/etc',
        ]);
    }

    public function testNotWritable2()
    {
        $this->setExpectedException('Exception', 'Cannot write to path.');
        $this->tester->execute([
            'command'  => $this->command->getName(),
            'name'     => 'Butts',
            'location' => '/etc/butts',
        ]);
    }

    public function testRelative()
    {
        $this->tester->execute([
            'command'  => $this->command->getName(),
            'name'     => 'Butts',
            'location' => 'butts',
        ]);
        $this->assertEquals(InitCommand::$successMessage, trim($this->tester->getDisplay()));
    }

    public function testExecuteException()
    {
        $this->command->migrationService->method('init')->will($this->throwException(new \Exception('Foo exception')));

        $this->tester->execute([
            'command'  => $this->command->getName(),
            'name'     => 'Butts',
            'location' => 'butts',
        ]);
        $this->assertEquals('Foo exception'.PHP_EOL, $this->tester->getDisplay());
    }
}
