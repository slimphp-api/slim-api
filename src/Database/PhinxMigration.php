<?php
namespace SlimApi\Database;

use Phinx\Migration\CreationInterface;

class PhinxMigration implements CreationInterface
{
    public static $commands = [];
    private $template = <<<'EOT'
<?php

use $useClassName;

class $className extends $baseClassName
{

    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     */
    public function change()
    {
        $commands
    }
}
EOT;

    public function getMigrationTemplate() {
        $template = $this->template;
        $template = strtr($template, ['$commands' => implode(PHP_EOL.'        ', static::$commands)]);
        $template = implode(PHP_EOL, array_map('rtrim', explode(PHP_EOL, $template)));
        return $template;
    }

    public function postMigrationCreation($migrationFilename, $className, $baseClassName) {

    }

}
