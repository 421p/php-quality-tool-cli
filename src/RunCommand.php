<?php

namespace QualityTool;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class RunCommand extends Command
{
    const APP_DEFAULT_CONFIG_FOLDER_PATH = '/app/Resources/GammaQualityTool';
    const APP_DEFAULT_CONFIG_FILE_NAME   = 'config.yml';

    protected static $defaultName = 'run';

    protected function configure()
    {
        $this
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
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $diffChecker = new Process('git diff --name-only');
        $diffChecker->start();
        $diffChecker->wait();

        $files = array_filter(explode(PHP_EOL, $diffChecker->getOutput()));

        $configFile = $input->getOption('config-file') ?? self::APP_DEFAULT_CONFIG_FILE_NAME;
        $configFolder = $input->getOption('config-folder') ?? self::APP_DEFAULT_CONFIG_FOLDER_PATH;

        $tool = new CodeQualityTool($files, getcwd(), $configFolder, $configFile);

        $tool->run($input, $output);

        if (!$tool->isCodeStyleViolated()) {
            $output->writeln('<fg=green>Success</>');
            exit(0);
        } else {
            $output->writeln('<error>Found errors! Aborting Commit.</error>');
            exit(1);
        }
    }
}
