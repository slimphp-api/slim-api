<?php
namespace SlimApi\Command;

use \Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Slim\App;

class RoutesCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('routes')
            ->setDescription('Retrieves routes for app.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (false === is_file('composer.json')) {
            throw new Exception('Commands must be run from root of project.');
        }

        try {
            $app  = new App();
            require 'src/routes.php';
            $routes = $app->getContainer()->get('router')->getRoutes();
            foreach ($routes as $route) {
                foreach ($route->getMethods() as $method) {
                    $name     = str_pad($route->getName(), 10, ' ', STR_PAD_LEFT);
                    $method   = str_pad($method, 8, ' ', STR_PAD_RIGHT);
                    $routeStr = str_pad($route->getPattern(), 15, ' ', STR_PAD_RIGHT);
                    $resolvesTo = (is_string($route->getCallable()) ? $route->getCallable() : '');
                    $output->writeln('<info>'.$name.$method.$routeStr.$resolvesTo.'</info>');
                }
            }
        } catch (Exception $e) {
            $output->writeln($e->getMessage());
        }
    }
}
