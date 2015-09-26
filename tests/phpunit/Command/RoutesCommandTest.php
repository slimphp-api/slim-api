<?php
namespace SlimApiTest\Command;

use SlimApi\Command\RoutesCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class RoutesCommandTest extends \PHPUnit_Framework_TestCase
{
    use \SlimApiTest\DirectoryTrait;

    protected $tester;
    protected $command;

    protected function setUp()
    {
        $application = new Application('SlimApi', '@package_version@');
        $application->add(new RoutesCommand);
        $this->command = $application->find('routes');
        $this->tester  = new CommandTester($this->command);
        $this->setupDirectory();
        $routesFileStr = <<<'EOT'
<?php
EOT;
        file_put_contents('src/routes.php', $routesFileStr);
    }

    public function testMustComposer()
    {
        chdir('src');
        $this->setExpectedException('Exception', 'Commands must be run from root of project.');
        $this->tester->execute(['command' => $this->command->getName()]);
    }

    // public function testNoRoutes()
    // {
    //     $this->tester->execute(['command' => $this->command->getName()]);
    //     $this->assertEmpty($this->tester->getDisplay());
    // }

    public function testMultipleRoutes()
    {
        $routesFileStr = <<<'EOT'
<?php
$app->map(['GET'], '/bar3', 'Fred\Controller\Bar3Controller:indexAction');
$app->map(['GET'], '/bar3/{id}', 'Fred\Controller\Bar3Controller:getAction');
$app->map(['POST'], '/bar3', 'Fred\Controller\Bar3Controller:postAction');
$app->map(['POST', 'PUT'], '/bar3/{id}', 'Fred\Controller\Bar3Controller:putAction');
$app->map(['DELETE'], '/bar3/{id}', 'Fred\Controller\Bar3Controller:deleteAction');
EOT;

        file_put_contents('src/routes.php', $routesFileStr);

        $routesOutputStr = <<<'EOT'
          GET     /bar3          Fred\Controller\Bar3Controller:indexAction
          GET     /bar3/{id}     Fred\Controller\Bar3Controller:getAction
          POST    /bar3          Fred\Controller\Bar3Controller:postAction
          POST    /bar3/{id}     Fred\Controller\Bar3Controller:putAction
          PUT     /bar3/{id}     Fred\Controller\Bar3Controller:putAction
          DELETE  /bar3/{id}     Fred\Controller\Bar3Controller:deleteAction

EOT;
        $this->tester->execute(['command' => $this->command->getName()]);
        $this->assertEquals($routesOutputStr, $this->tester->getDisplay());

    }

    public function testExecuteException()
    {
        $routesFileStr = <<<'EOT'
<?php
throw new Exception('Foo exception');
EOT;
        file_put_contents('src/routes.php', $routesFileStr);

        $this->tester->execute(['command' => $this->command->getName()]);
        $this->assertEquals('Foo exception'.PHP_EOL, $this->tester->getDisplay());
    }
}
