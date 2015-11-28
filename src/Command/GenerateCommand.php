<?php
namespace SlimApi\Command;

use \Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
    /**
     * @param mixed $generatorFactory
     *
     * @return
     */
    public function __construct($generatorFactory)
    {
        parent::__construct();
        $this->generatorFactory = $generatorFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('generate')
            ->setAliases(['g'])
            ->setDescription('Creates a default slim-api application.')
            ->addArgument(
                'type',
                InputArgument::REQUIRED,
                'What type of generator? [scaffold, controller, model].'
            )
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Resultant resource name.'
            )
            ->addArgument(
                'fields',
                InputArgument::IS_ARRAY,
                'What fields (if appropriate).'
            )
            ->addOption(
                'api-version',
                null,
                InputOption::VALUE_OPTIONAL,
                "When creating scaffold/controller/route this surrounds the route in a version"
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument('type');
        if (!in_array($type, ['scaffold', 'controller', 'model', 'migration'])) {
            throw new Exception('Invalid type.');
        }

        $name    = ucfirst($input->getArgument('name'));
        $pattern = '/^[A-Z][a-zA-Z0-9]*$/';
        if (1 !== preg_match($pattern, $name)) {
            throw new Exception('Invalid name.');
        }

        $fields = $input->getArgument('fields');
        if (!is_array($fields)) {
            throw new Exception('Invalid fields.');
        }

        if (false === is_file('composer.json')) {
            throw new Exception('Commands must be run from root of project.');
        }

        $version = $input->getOption('api-version');

        $generator = $this->generatorFactory->fetch($type);
        if (!$generator->validate($name, $fields)) {
            throw new Exception('Fields not valid.');
        }

        try {
            $generator->process($name, $fields, ['version' => $version]);
            $output->writeln('<info>Generation completed.</info>');
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return true;
    }
}
