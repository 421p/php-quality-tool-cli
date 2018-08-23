<?php

namespace QualityTool;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class CliRunner extends Application
{
    const DEFAULT_COMMAND_NAME = 'run';
    const APP_DEFAULT_CONFIG_FOLDER_PATH = '/app/Resources/GammaQualityTool';
    const APP_DEFAULT_CONFIG_FILE_NAME = 'config.yml';

    public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);

        $this->add($this->createDefaultCommand());
        $this->setDefaultCommand(self::DEFAULT_COMMAND_NAME, true);
    }

    private function configure(Command $command)
    {
        $command
            ->addOption(
                'config-folder',
                null,
                InputOption::VALUE_REQUIRED,
                'Folder of config file.',
                null
            )
            ->addOption(
                'config-file',
                null,
                InputOption::VALUE_REQUIRED,
                'Name of config file.',
                null
            )
            ->addOption(
                'no-commit-autofixed',
                null,
                InputOption::VALUE_NONE,
                'Disables adding autofixed files via "git add"'
            );
    }

    private function execute(InputInterface $input, OutputInterface $output)
    {
        $diffChecker = new Process('git diff --name-only');
        $diffChecker->start();
        $diffChecker->wait();

        $files = array_filter(explode(PHP_EOL, $diffChecker->getOutput()));

        $configFile = $input->getOption('config-file') ?? CliRunner::APP_DEFAULT_CONFIG_FILE_NAME;
        $configFolder = $input->getOption('config-folder') ?? CliRunner::APP_DEFAULT_CONFIG_FOLDER_PATH;
        $commitAutofixed = !$input->getOption('no-commit-autofixed');

        $tool = new CodeQualityTool($files, getcwd(), $configFolder, $configFile, $commitAutofixed);
        $tool->setAutoExit(false);
        $tool->run($input, $output);

        if ($tool->isCodeStyleViolated()) {
            $output->writeln('<error>Found errors! Aborting Commit.</error>');

            return 1;
        }

        $output->writeln('<fg=green>Success</>');

        return 0;
    }

    private function createDefaultCommand()
    {
        $configurator = \Closure::fromCallable([$this, 'configure']);
        $executor = \Closure::fromCallable([$this, 'execute']);

        return new class($configurator, $executor) extends Command {
            private $configurator;
            private $executor;

            public function __construct(
                callable $configurator,
                callable $executor,
                string $name = CliRunner::DEFAULT_COMMAND_NAME
            ) {
                $this->configurator = $configurator;
                $this->executor = $executor;
                parent::__construct($name);
            }

            protected function configure()
            {
                ($this->configurator)($this);
            }

            protected function execute(InputInterface $input, OutputInterface $output)
            {
                return ($this->executor)($input, $output);
            }
        };
    }
}
