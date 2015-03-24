<?php namespace Laradic\Docit\Console;

use Illuminate\Console\Command;
use Illuminate\Support\ServiceProvider;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class CreateProjectCommand extends Command
{
    protected $name = 'docs:create-project';
    protected $description = 'Command description.';
    public function __construct()
    {
        parent::__construct();
    }

    public function fire()
    {
        $this->comment($this->name . ' has been executed');

    }

}
