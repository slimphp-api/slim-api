<?php
namespace SlimApiTest\Service;

use SlimApi\Service\RouteService;

class RouteServiceTest extends \PHPUnit_Framework_TestCase
{
    use \SlimApiTest\DirectoryTrait;
    public function setUp()
    {
        $routerFileLocation = 'src/routes.php';

        $this->setupDirectory();
        $routes = <<<'EOT'
<?php
// Routes
EOT;
        file_put_contents($routerFileLocation, $routes);

        $routeTemplate = <<<'EOT'
$app->map($methodMap, '$route', '$controllerCallable');
EOT;

        $this->routeService = new RouteService($routerFileLocation, $routeTemplate, 'Foo');
    }

    public function testIndexAction()
    {
        $this->routeService->processCommand('addRoute', 'addRoute', 'index');
        $this->assertEquals($this->routeService->commands, ['$app->map([\'GET\'], \'$route\', \'Foo\Controller\$nameController:indexAction\');']);
    }

    public function testGetAction()
    {
        $this->routeService->processCommand('addRoute', 'addRoute', 'get');
        $this->assertEquals($this->routeService->commands, ['$app->map([\'GET\'], \'$route/{id}\', \'Foo\Controller\$nameController:getAction\');']);
    }

    public function testPostAction()
    {
        $this->routeService->processCommand('addRoute', 'addRoute', 'post');
        $this->assertEquals($this->routeService->commands, ['$app->map([\'POST\'], \'$route\', \'Foo\Controller\$nameController:postAction\');']);
    }

    public function testPutAction()
    {
        $this->routeService->processCommand('addRoute', 'addRoute', 'put');
        $this->assertEquals($this->routeService->commands, ['$app->map([\'POST\', \'PUT\'], \'$route/{id}\', \'Foo\Controller\$nameController:putAction\');']);
    }

    public function testDeleteAction()
    {
        $this->routeService->processCommand('addRoute', 'addRoute', 'delete');
        $this->assertEquals($this->routeService->commands, ['$app->map([\'DELETE\'], \'$route/{id}\', \'Foo\Controller\$nameController:deleteAction\');']);
    }

    public function testInvalidAction()
    {
        $this->setExpectedException('Exception', 'Invalid method.foo');
        $this->routeService->processCommand('addRoute', 'addRoute', 'foo');
    }

    public function testTargetLocation()
    {
        $this->assertEquals($this->routeService->targetLocation('foo'), 'src/routes.php');
    }

}
