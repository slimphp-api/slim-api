<?php
namespace SlimApi\Service;

use SlimApi\Interfaces\GeneratorServiceInterface;

class DependencyService implements GeneratorServiceInterface
{
    public $commands = [];

    public function __construct($dependencyFileLocation, $controllerDependencyTemplate, $modelDependencyTemplate, $namespaceRoot)
    {
        $this->dependencyFileLocation       = $dependencyFileLocation;
        $this->controllerDependencyTemplate = $controllerDependencyTemplate;
        $this->modelDependencyTemplate      = $modelDependencyTemplate;
        $this->namespaceRoot                = $namespaceRoot;
    }

    public function processCommand($type, ...$arguments)
    {
        $name = array_shift($arguments);
        switch ($type) {
            case 'injectController':
                $this->addDependency($name, $this->controllerDependencyTemplate);
                break;
            case 'injectModel':
                $this->addDependency($name, $this->modelDependencyTemplate);
                break;
            default:
                throw new \Exception('Invalid dependency command.');
                break;
        }
    }

    public function create($name)
    {
        $content        = PHP_EOL.implode(PHP_EOL.PHP_EOL, $this->commands);
        $this->commands = [];
        return file_put_contents($this->targetLocation($name), $content, FILE_APPEND);
    }

    public function targetLocation($name)
    {
        return $this->dependencyFileLocation;
    }

    private function addDependency($name, $template)
    {
        $this->commands[] = strtr($template, ['$namespace' => $this->namespaceRoot, '$name' => $name]);
    }
}