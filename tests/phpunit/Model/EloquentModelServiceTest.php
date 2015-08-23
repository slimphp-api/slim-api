<?php
namespace SlimApiTest\Model;

use \SlimApi\Model\EloquentModelService;

class EloquentModelServiceTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $modelTemplate = <<<'EOT'
<?php
namespace $namespace\Model;

use \Illuminate\Database\Eloquent\Model;

class $name extends Model
{
}
EOT;

        $this->eloquentModelService = new EloquentModelService($modelTemplate, 'FooTest');

        chdir(__DIR__.'/../output');
        if (file_exists('project0')) {
            exec('rm -rf project0');
        }
        mkdir('project0');
        mkdir('project0/src');
        mkdir('project0/src/Model');
        chdir('project0/');
    }

    public function testCreate()
    {
        $modelStr =<<<'EOT'
<?php
namespace FooTest\Model;

use \Illuminate\Database\Eloquent\Model;

class Bar extends Model
{
}
EOT;

        $this->eloquentModelService->create('Bar');
        $this->assertEquals($modelStr, file_get_contents('src/Model/BarModel.php'));
    }

    public function testTargetLocation()
    {
        $this->assertEquals('src/Model/FooModel.php', $this->eloquentModelService->targetLocation('Foo'));
    }
}
