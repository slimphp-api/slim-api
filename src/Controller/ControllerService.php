<?php
namespace SlimApi\Controller;

use SlimApi\Interfaces\GeneratorServiceInterface;

class ControllerService implements ControllerInterface, GeneratorServiceInterface
{
    public  $commands  = [];
    private $templates = [];
    private $namespace = '';

    /**
     * @param string $indexTemplate
     * @param string $getTemplate
     * @param string $postTemplate
     * @param string $putTemplate
     * @param string $deleteTemplate
     * @param string $classTemplate
     * @param string $constructorTemplate
     * @param string $namespace
     */
    public function __construct($indexTemplate, $getTemplate, $postTemplate, $putTemplate, $deleteTemplate, $classTemplate, $constructorTemplate, $namespace)
    {
        $this->templates['index']       = $indexTemplate;
        $this->templates['get']         = $getTemplate;
        $this->templates['post']        = $postTemplate;
        $this->templates['put']         = $putTemplate;
        $this->templates['delete']      = $deleteTemplate;
        $this->templates['class']       = $classTemplate;
        $this->templates['constructor'] = $constructorTemplate;
        $this->namespace                = $namespace;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function create($name)
    {

        $consTemplate = $this->templates['constructor'];
        if (strlen($consTemplate) > 0) {
            $consTemplate = preg_filter('/^/', '    ', explode(PHP_EOL, $consTemplate));
            $consTemplate = implode(PHP_EOL, $consTemplate).PHP_EOL;
        }

        $template = $this->templates['class'];
        $content  = strtr($template, [
            '$namespace'   => $this->namespace,
            '$name'        => $name,
            '$commands'    => implode(PHP_EOL.PHP_EOL, $this->commands),
            '$constructor' => $consTemplate
        ]);

        return file_put_contents($this->targetLocation($name), $content);
    }

    /**
     * {@inheritdoc}
     */
    public function targetLocation($name)
    {
        return 'src/Controller/'.$name.'Controller.php';
    }

    /**
     * Parses and adds a controller action
     *
     * @param string $actionName
     *
     * @return
     */
    private function addAction($actionName)
    {
        $template         = $this->templates[$actionName];
        $command          = preg_filter('/^/', '    ', explode(PHP_EOL, strtr($template, ['$actionName' => $actionName])));
        $command          = array_filter($command, function($var){return strlen(trim($var));});
        $command          = implode(PHP_EOL, $command);
        $this->commands[] = $command;
    }
}
