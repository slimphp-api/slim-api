<?php
namespace SlimApi\Database;

interface DatabaseInterface
{
    public function init($directory);
    public function create($name);
    public function addMigrationCommand($type, ...$arguments);
}
