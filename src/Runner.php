<?php

namespace QualityTool;

use Symfony\Component\Console\Application;

class Runner extends Application
{
    public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);

        $this->add(new RunCommand());
        $this->setDefaultCommand('run', true);
    }
}
