<?php
namespace SlimApiTest\Service;

use SlimApi\Service\ConfigService;

class ConfigServiceTest extends \PHPUnit_Framework_TestCase
{
    use \SlimApiTest\DirectoryTrait;

    public function setUp()
    {
        $this->setupDirectory();
        $config = <<<'EOT'
<?php
return [
    'namespace' => 'Fred',
    'slim-api' => [
        'modules' => [
            'SlimPhinx', //provides migrations
            'SlimEloquent' //provides ORM
        ]
    ]
];
EOT;
        file_put_contents('config/slim-api.config.php', $config);

        $this->configService = new ConfigService;
    }

    public function testConfigFile()
    {
        $configArray = [
            'namespace' => 'Fred',
            'slim-api' => [
                'modules' => [
                    'SlimPhinx', //provides migrations
                    'SlimEloquent' //provides ORM
                ]
            ]
        ];

        $this->assertEquals($configArray, $this->configService->fetch());
    }
}
