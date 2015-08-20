<?php
namespace SlimApi\Command;

use \Exception;
use SlimApi\Skeleton\SkeletonInterface;
use SlimApi\Database\DatabaseInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    public static $successMessage = "Application correctly created. Don't forget to run composer install!";
    public static $failureMessage = "Application failed to create.";

    public function __construct(SkeletonInterface $skeletonService, DatabaseInterface $databaseService)
    {
        parent::__construct();
        $this->skeletonService = $skeletonService;
        $this->databaseService = $databaseService;
    }

    protected function configure()
    {
        $this
            ->setName('init')
            ->setDescription('Creates a default slim-api application.')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Application name (becomes namespace).'
            )
            ->addArgument(
                'location',
                InputArgument::OPTIONAL,
                'Where do you want to create the application?',
                getcwd()
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name    = ucfirst($input->getArgument('name'));
        $pattern = '/^[A-Z][a-zA-Z0-9]*$/';
        if (1 !== preg_match($pattern, $name)) {
            throw new Exception('Invalid name.');
        }

        $path       = $input->getArgument('location');
        $path       = ('/' === $path[0]) ? $path : getcwd().'/'.$path;
        $pathExists = is_dir($path);
        $pathInfo   = pathinfo($path);
        if (
            ($pathExists && !is_writable($path)) ||
            (!$pathExists && !is_writable($pathInfo['dirname']))
        ) {
            throw new Exception('Cannot write to path.');
        }

        if ($pathExists && 2 !== count(scandir($path))) {
            throw new Exception('Path not empty.');
        }

        try {
            $this->skeletonService->create($path, $name);
            $this->databaseService->init($path);
            $output->writeln('<info>'.static::$successMessage.'</info>');
        } catch (Exception $e) {
            $output->writeln($e->getMessage());
        }
    }
}
