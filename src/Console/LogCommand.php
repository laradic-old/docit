<?php namespace Laradic\Docit\Console;

use Config;
use File;

class LogCommand extends BaseCommand
{

    protected $name = 'docit:log';

    protected $description = 'Show the Docit log.';

    public function fire()
    {
        /** @var \Laradic\Docit\Log\Writer $logger */
        $logger = app('docit.log');
        print Config::get('laradic/config::logger.path');
        $log = File::get(Config::get('laradic/config::logger.path'));
        print $log;
    }
}
