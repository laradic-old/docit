<?php namespace Laradic\Docit\Console;

use Laradic\Docit\Contracts\ProjectFactory;
use Laradic\Console\Command;

abstract class BaseCommand extends Command
{

    /** @var \Laradic\Docit\Projects\ProjectFactory */
    protected $projects;

    public function __construct(ProjectFactory $projects)
    {
        parent::__construct();
        $this->projects = $projects;
    }
}
