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
            ->setHelp(<<<EOT
The <info>generate</info> command help to generate controllers/models/routes or all together as scaffold.
Model/scaffold generators take the db definition as extra arguments, in the form name:type:length:nullable:unique in most cases.
<info>slimapi generate scaffold user oauthToken:text::false:true token:string:128:false:true role:string:128:false</info>
In the case of the type being reference the name should be the reference, and it also takes extra arguments for action when foreign key deleted and updated.
<info>slimapi generate scaffold scaffold foo field1:integer field2:string user:reference</info>
<info>slimapi generate scaffold scaffold bar field1:integer field2:string user:reference:::NO_ACTION:CASCADE</info>
EOT
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
