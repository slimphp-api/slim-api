<?php
namespace SlimApi\Generator;

class ModelGenerator implements GeneratorInterface
{
    public function __construct($migrationService, $modelService)
    {
        $this->migrationService = $migrationService;
        $this->modelService     = $modelService;
    }

    public function validate($name, $fields)
    {
        if ($this->modelExists($name)) {
            return false;
        }
        return true;
    }

    public function process($name, $fields)
    {
        $this->processCreateMigration($name, $fields);
        $this->processCreateModel($name, $fields);
    }

    private function processCreateMigration($name, $fields)
    {
        $this->migrationService->addMigrationCommand('create', ucfirst($name));

        // name:type:limit:null:unique
        foreach ($fields as $fieldDefinition) {
            $fieldDefinition = explode(':', $fieldDefinition);
            $fieldDefinition = array_map(function($value) {
                return (strlen($value) === 0 ? NULL : $value);
            }, $fieldDefinition);
            $this->migrationService->addMigrationCommand('addColumn', ...$fieldDefinition);
        }

        $this->migrationService->addMigrationCommand('finalise');
        $this->migrationService->create($name);
    }

    private function processCreateModel($name)
    {
        $this->modelService->create($name);
    }

    private function modelExists($name)
    {
        // cwd should also be namespace
        // perhaps we could also parse the composer.json for
        // ['autoload']['psr-4'] $key?
        return is_file('src/Model/'.$name.'.php');
    }
}