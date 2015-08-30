<?php
namespace SlimApi\Generator;

use SlimApi\Database\DatabaseInterface;
use SlimApi\Model\ModelInterface;

class ModelGenerator implements GeneratorInterface
{
    public function __construct(DatabaseInterface $migrationService, ModelInterface $modelService)
    {
        $this->migrationService = $migrationService;
        $this->modelService     = $modelService;
    }

    public function validate($name, $fields)
    {
        $name = ucfirst($name);
        if ($this->modelExists($name)) {
            return false;
        }
        return true;
    }

    public function process($name, $fields)
    {
        $name = ucfirst($name);
        $this->processCreateMigration($name, $fields);
        $this->processCreateModel($name, $fields);
    }

    private function processCreateMigration($name, $fields)
    {
        $this->migrationService->processCommand('create', $name);

        // name:type:limit:null:unique
        foreach ($fields as $fieldDefinition) {
            $fieldDefinition = explode(':', $fieldDefinition);
            $fieldDefinition = array_map(function($value) {
                return (strlen($value) === 0 ? NULL : $value);
            }, $fieldDefinition);
            $this->migrationService->processCommand('addColumn', ...$fieldDefinition);
            $this->modelService->processCommand('addColumn', ...$fieldDefinition);
        }

        $this->migrationService->processCommand('finalise');
        $this->migrationService->create($name);
    }

    private function processCreateModel($name)
    {
        $this->modelService->create($name);
    }

    private function modelExists($name)
    {
        return is_file($this->modelService->targetLocation($name));
    }
}
