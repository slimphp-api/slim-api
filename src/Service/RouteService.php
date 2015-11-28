<?php
namespace SlimApi\Service;

use SlimApi\Interfaces\GeneratorServiceInterface;

class RouteService implements GeneratorServiceInterface
{
    public $commands = [];

    /**
     * @param string $routerFileLocation
     * @param string $routeTemplate
     * @param string $namespaceRoot
     *
     * @return
     */
    public function __construct($routerFileLocation, $routeTemplate, $namespaceRoot)
    {
        $this->template           = $routeTemplate;
        $this->routerFileLocation = $routerFileLocation;
        $this->controllerLocation = $namespaceRoot.'\Controller\$nameController';
    }

    /**
     * {@inheritdoc}
     */
    public function processCommand($type, ...$arguments)
    {
        // $arguments is method1, method2 etc
        array_shift($arguments);
        switch ($type) {
            case 'addRoute':
                foreach ($arguments as $method) {
                    $this->addRoute($method);
                }
                break;
            default:
                throw new \Exception('Invalid route command.');
                break;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function create($name, $options)
    {
        $version = '/'.(array_key_exists('version', $options) ? $options['version'] : '');
        $content = PHP_EOL.PHP_EOL.implode(PHP_EOL, $this->commands);
        $content = strtr($content, ['$route' => '/'.strtolower($name), '$name' => $name, '$version' => $version]);
        return file_put_contents($this->targetLocation($name), $content, FILE_APPEND);
    }

    /**
     * {@inheritdoc}
     */
    public function targetLocation($name)
    {
        return $this->routerFileLocation;
    }

    /**
     * Process the requrested method into a route template
     *
     * @param string $method
     *
     * @return void
     */
    private function addRoute($method)
    {
        switch ($method) {
            case 'index':
                $methodMap          = ['GET'];
                $realRoute          = '$route';
                $controllerCallable = $this->controllerLocation.':indexAction';
                break;
            case 'get':
                $methodMap          = ['GET'];
                $realRoute          = '$route/{id}';
                $controllerCallable = $this->controllerLocation.':getAction';
                break;
            case 'post':
                $methodMap          = ['POST'];
                $realRoute          = '$route';
                $controllerCallable = $this->controllerLocation.':postAction';
                break;
            case 'put':
                $methodMap          = ['POST', 'PUT'];
                $realRoute          = '$route/{id}';
                $controllerCallable = $this->controllerLocation.':putAction';
                break;
            case 'delete':
                $methodMap          = ['DELETE'];
                $realRoute          = '$route/{id}';
                $controllerCallable = $this->controllerLocation.':deleteAction';
                break;
            default:
                throw new \Exception('Invalid method.'.$method);
                break;
        }
        $methodMap = "['".implode("', '", $methodMap)."']";
        $command   = strtr($this->template, ['$methodMap' => $methodMap, '$route' => $realRoute, '$controllerCallable' => $controllerCallable]);
        // $app->map(['GET'], '/trails', 'EarlyBird\Controllers\TrailsController:index');
        // $app->map(['GET'], '/texts/{stage}', 'EarlyBird\Controllers\TrailsController:get');
        // $app->map(['POST'], '/trails', 'EarlyBird\Controllers\TrailsController:post');
        // $app->map(['POST', 'PUT'], '/trails/{id}', 'EarlyBird\Controllers\TrailsController:put');
        $this->commands[] = $command;
    }
}
