<?php
namespace SlimApi\Service;

use SlimApi\Interfaces\GeneratorServiceInterface;

class DependencyService implements GeneratorServiceInterface
{
    public $commands   = [];
    private $templates = [];

    /**
     * @param string $dependencyFileLocation
     * @param string $namespaceRoot
     *
     * @return void
     */
    public function __construct($dependencyFileLocation, $namespaceRoot)
    {
        $this->dependencyFileLocation       = $dependencyFileLocation;
        $this->namespaceRoot                = $namespaceRoot;
    }

    /**
     * {@inheritdoc}
     */
    public function processCommand($type, ...$arguments)
    {
        $name = array_shift($arguments);
        $template = $this->fetch($type);
        if ($template) {
            $this->addDependency($name, $template);
        } else {
            throw new \Exception('Invalid dependency command.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function create($name, $options = [])
    {
        $content        = PHP_EOL.implode(PHP_EOL.PHP_EOL, $this->commands);
        $this->commands = [];
        $origContent = file($this->targetLocation($name));
        // insert content just before the return statement
        // @todo: something neater?
        array_splice($origContent, count($origContent)-2, 0, $content);
        return file_put_contents($this->targetLocation($name), $origContent);
    }

    /**
     * {@inheritdoc}
     */
    public function targetLocation($name)
    {
        return $this->dependencyFileLocation;
    }

    /**
     * Add the dependency processed template to our command array
     *
     * @param string $name
     * @param string $template
     *
     * @return void
     */
    private function addDependency($name, $template)
    {
        $this->commands[] = strtr($template, ['$namespace' => $this->namespaceRoot, '$name' => $name]);
    }

    /**
     * Fetches appropriate template
     *
     * @param string $name
     *
     * @return GeneratorInterface|false the required template or false if none.
     */
    public function fetch($name)
    {
        $template = false;
        if (array_key_exists($name, $this->templates)) {
            $template = $this->templates[$name];
        }
        return $template;
    }

    /**
     * Add a template to the factory
     *
     * @param string   $name      the name of the Generator
     * @param string   $template  the template to return for the specified name
     */
    public function add($name, $template)
    {
        if (array_key_exists($name, $this->templates)) {
            throw new \InvalidArgumentException('Template already exists.');
        }

        $this->templates[$name] = $template;
    }
}
