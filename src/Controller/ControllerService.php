<?php
namespace SlimApi\Controller;

use SlimApi\Interfaces\GeneratorServiceInterface;

class ControllerService implements ControllerInterface, GeneratorServiceInterface
{
    public  $commands  = [];
    private $templates = [];
    private $namespace = '';

    public function __construct($indexTemplate, $getTemplate, $postTemplate, $putTemplate, $deleteTemplate, $classTemplate, $namespace)
    {
        $this->templates['index']  = $indexTemplate;
        $this->templates['get']    = $getTemplate;
        $this->templates['post']   = $postTemplate;
        $this->templates['put']    = $putTemplate;
        $this->templates['delete'] = $deleteTemplate;
        $this->templates['class']  = $classTemplate;
        $this->namespace           = $namespace;
    }

    public function processCommand($type, ...$arguments)
    {
        switch ($type) {
            case 'addAction':
                foreach ($arguments as $actionName) {
                    $this->addAction($actionName);
                }
                break;
            default:
                throw new \Exception('Invalid command type.');
                break;
        }
    }

    public function create($name)
    {
        $template = $this->templates['class'];
        $content  = strtr($template, ['$namespace' => $this->namespace, '$name' => $name, '$commands' => implode(PHP_EOL.'    ', $this->commands)]);
        $content = implode(PHP_EOL, array_map('rtrim', explode(PHP_EOL, $content)));
        return file_put_contents('src/Controller/'.$name.'Controller.php', $content);
    }

    public function targetLocation($name)
    {
        return 'src/Controller/'.$name.'Controller.php';
    }

    private function addAction($actionName)
    {
        $template         = $this->templates[$actionName];
        $command          = strtr($template, ['$actionName' => $actionName]);
        $this->commands[] = $command;
    }
}
