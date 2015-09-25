<?php
namespace SlimApi\Command\Generate;

use \Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use SlimApi\Generator\GeneratorInterface;
use SlimApi\Command\Traits\GeneratorTrait;

class GenerateControllerCommand extends Command
{
    use GeneratorTrait;

    /**
     * @param GeneratorInterface $generator
     *
     * @return
     */
    public function __construct(GeneratorInterface $generator)
    {
        parent::__construct();
        $this->generator = $generator;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('generate:controller')
            ->setDescription('Creates a controller.')
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
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name   = ucfirst($input->getArgument('name'));
        $fields = $input->getArgument('fields');

        try {
            $this->validate($name, $fields);
            $this->generator->process($name, $fields);
            $output->writeln('<info>Generation completed.</info>');
        } catch (Exception $e) {
            $output->writeln('<info>'.$e->getMessage().'</info>');
            return 1;
        }

        return 0;
    }
}
