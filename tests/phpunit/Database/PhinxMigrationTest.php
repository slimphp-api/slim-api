<?php
namespace SlimApiTest\Database;

use SlimApi\Database\PhinxMigration;

class PhinxMigrationTest extends \PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        $this->phinxMigration = new PhinxMigration;
    }

    public function testGetMigrationTemplate()
    {
        PhinxMigration::$commands = ['$table = $this->table("Foo");',
            '$table->addColumn("one", "integer");',
            '$table->create();'];
        $template = <<<'EOT'
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
        $table = $this->table("Foo");
        $table->addColumn("one", "integer");
        $table->create();
    }
}
EOT;
        $this->assertEquals($template, $this->phinxMigration->getMigrationTemplate());
    }
}
