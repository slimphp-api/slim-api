<?php
namespace SlimApi\Command\Traits;

use \Exception;
use \Fusions\Tag;
use \Fusions\Type;

trait GeneratorTrait
{
    public function validate($name, $fields)
    {
        $pattern = '/^[A-Z][a-zA-Z0-9]*$/';
        if (1 !== preg_match($pattern, $name)) {
            throw new Exception('Invalid name.');
        }

        if (!is_array($fields)) {
            throw new Exception('Invalid fields.');
        }

        if (false === is_file('composer.json')) {
            throw new Exception('Commands must be run from root of project.');
        }

        if (!$this->generator->validate($name, $fields)) {
            throw new Exception('Fields not valid.');
        }
    }
}
