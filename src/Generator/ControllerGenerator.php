<?php
namespace SlimApi\Generator;

use SlimApi\Controller\ControllerInterface;
use SlimApi\Interfaces\GeneratorServiceInterface;

class ControllerGenerator implements GeneratorInterface
{
    private $validActions = ['index', 'get', 'post', 'put', 'delete'];

    public function __construct(ControllerInterface $controllerService, GeneratorServiceInterface $routeService, GeneratorServiceInterface $dependencyService)
    {
        $this->controllerService = $controllerService;
        $this->routeService      = $routeService;
        $this->dependencyService = $dependencyService;
    }

    public function validate($name, $fields)
    {
        $name = ucfirst($name);
        if ($this->controllerExists($name)) {
            return false;
        }

        foreach ($fields as $possibleAction) {
            if (!in_array($possibleAction, $this->validActions)) {
                return false;
            }
        }

        return true;
    }

    public function process($name, $fields)
    {
        $name = ucfirst($name);
        if (0 === count($fields)) {
            $fields = $this->validActions;
        }

        foreach ($fields as $action) {
            $this->controllerService->processCommand('addAction', $action);
        }

        $this->routeService->processCommand('addRoute', '/'.$name, ...$fields);
        $this->dependencyService->processCommand('injectController', $name);

        $this->controllerService->create($name);
        $this->routeService->create($name);
        $this->dependencyService->create($name);
    }

    private function controllerExists($name)
    {
        return is_file($this->controllerService->targetLocation($name));
    }
}
