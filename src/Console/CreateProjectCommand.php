<?php namespace Laradic\Docit\Console;

class CreateProjectCommand extends BaseCommand
{

    protected $name = 'docit:create-project';

    protected $description = 'Create a new project.';

    public function fire()
    {
        $this->comment($this->name . ' has been executed');
    }
}
