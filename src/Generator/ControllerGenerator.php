<?php
namespace SlimApi\Generator;

class ControllerGenerator implements GeneratorInterface
{
    private $validActions = ['index', 'get', 'post', 'put', 'delete'];

    public function __construct($controllerService)
    {
        $this->controllerService = $controllerService;
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

        $this->controllerService->create($name);
    }

    private function controllerExists($name)
    {
        return is_file($this->controllerService->targetLocation($name));
    }
}
