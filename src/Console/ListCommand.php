<?php namespace Laradic\Docit\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Laradic\Support\AbstractConsoleCommand;
use Laradic\Support\Arr;
use Laradic\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ListCommand extends AbstractConsoleCommand
{

    protected $name = 'docs:list';

    protected $description = 'Command description.';

    /** @var \Laradic\Docit\ProjectFactory */
    protected $projects;

    public function __construct(Application $app)
    {
        parent::__construct();
        $this->projects = $app->make('Laradic\Docit\Contracts\ProjectFactory');
    }

    public function fire()
    {

        $rows = [];
        foreach ($this->projects->all() as $project)
        {
            $p              = $this->projects->make($project['slug']);
            $defaultVersion = $p->getDefaultVersion();
            $versions       = Arr::replaceValue($p->getSortedVersions(), $defaultVersion, $this->colorize(['cyan', 'bold'], $defaultVersion));
            $github         = isset($project['github']) && $project['github']['enabled'] == true ? $project['github']['username'] . '/' . $project['github']['repository'] : '-';
            $path           = Str::remove($project['path'], public_path() . '/');

            $rows[] = [$project['title'], $project['slug'], $github, join(', ', $versions), $path];
        }


        $this->table(['title', 'slug', 'github', 'versions', 'path'], $rows);
        #Debugger::dump();
    }
}
