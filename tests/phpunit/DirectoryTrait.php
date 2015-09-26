<?php
namespace SlimApiTest;
trait DirectoryTrait
{
    private function setupDirectory()
    {
        $composerContent = '{"autoload":{"psr-4": {"Project0\\\": "src/"}}}';
        chdir(__DIR__.'/output');
        if (file_exists('project0')) {
            exec('rm -rf project0');
        }
        mkdir('project0');
        mkdir('project0/src');
        mkdir('project0/config');
        mkdir('project0/src/Controller');
        mkdir('project0/src/Model');
        file_put_contents('project0/composer.json', $composerContent);
        file_put_contents('project0/src/routes.php', '<?php'.PHP_EOL);
        chdir('project0/');
    }
}

 ?>
