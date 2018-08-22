<?php

namespace QualityTool;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class RunCommand extends Command
{
    protected static $defaultName = 'run';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $diffChecker = new Process('git diff --name-only');
        $diffChecker->start();
        $diffChecker->wait();

        $files = array_filter(explode(PHP_EOL, $diffChecker->getOutput()));

        $tool = new CodeQualityTool($files, getcwd());

        $tool->run($input, $output);
    }
}
