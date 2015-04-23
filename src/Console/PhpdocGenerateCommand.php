<?php namespace Laradic\Docit\Console;

use Illuminate\Console\Command;

class PhpdocGenerateCommand extends BaseCommand
{
    protected $name = 'docit:phpdoc';

    protected $description = 'Generate PHPDoc for all enabled projects.';

    public function fire()
    {
        $this->comment($this->name . ' has been executed');
    }
}
