<?php

namespace App\Command;

use App\Service\TaskManager;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// the "name" and "description" arguments of AsCommand replace the
// static $defaultName and $defaultDescription properties
#[AsCommand(
    name: 'task:process',
    description: 'Process tasks list.',
    hidden: false,
)]
class ProcessTasksCommand extends Command
{
    // the command description shown when running "php bin/console list"
    protected static $defaultDescription = 'Process tasks list.';

    private TaskManager $taskManager;


    public function __construct( TaskManager $taskManager)
    {
        // best practices recommend to call the parent constructor first and
        // then set your own properties. That wouldn't work in this case
        // because configure() needs the properties set in this constructor

        $this->taskManager = $taskManager;

        parent::__construct();
    }
    
    protected function configure(): void
    {
        $this
            // the command help shown when running the command with the "--help" option
            ->setHelp('This command allows you to process tasks')
            ->addArgument('filename', InputArgument::REQUIRED, 'The filename with tasks list in JSON format.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            // outputs multiple lines to the console (adding "\n" at the end of each line)
            $output->writeln([
                'Processing of tasks',
                '============',
                '',
            ]);

//            $filePath = 'recruitment-task-source.json';
            $filename = $input->getArgument('filename');
            if (str_contains($filename, '/')) {
                $output->writeln(['<error>Specify a file name, not a path!</error>']);
                return Command::FAILURE;
            }
            $filename = str_replace("/","",$filename);

            $output->writeln(["Filename: {$filename}", '']);

            $tasksJSON = $this->taskManager->getTasksFromFile($filename);

            $returnValue = $this->taskManager->processTasks($tasksJSON);

            $output->writeln("<info>Success</info>");
            $output->writeln("Processed tasks: {$returnValue['allItemsCount']}");
            $output->writeln("The number of reviews created: {$returnValue['numberOfReview']}");
            $output->writeln("Number of failure notifications created: {$returnValue['numberOfReportList']}");
            $output->writeln('');
            $output->writeln("<error>Number of unprocessed tasks: " . count($returnValue['unprocessedList'] ). "</error>");
            $output->writeln('List:');
            foreach($returnValue['unprocessedList'] as $item) {
                $output->writeln("Number: {$item['number']} > {$item['error_message']}");
            }

            return Command::SUCCESS;
        } catch (Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');

            return Command::FAILURE;
        }
    }
}