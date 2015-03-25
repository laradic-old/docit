<?php namespace Laradic\Docit\Console;

use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Laradic\Docit\Github\GithubProjectSynchronizer;
use Laradic\Support\AbstractConsoleCommand;
use Laradic\Support\Arr;
use Laradic\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class GithubSyncCommand extends AbstractConsoleCommand
{

    protected $name = 'docs:github-sync';

    protected $description = 'Command description.';

    /** @var \Laradic\Docit\ProjectFactory */
    protected $projects;

    protected $github;

    protected $syncer;

    public function __construct(Application $app)
    {
        parent::__construct();
        $this->github   = $app->make('GrahamCampbell\GitHub\GitHubManager');
        $this->projects = $app->make('Laradic\Docit\Contracts\ProjectFactory');
        $this->syncer   = new GithubProjectSynchronizer($this->github, $this->projects);
    }

    public function fire()
    {
        $githubProjects = [];
        $choices        = [];
        foreach ($this->projects->all() as $project)
        {
            if ( isset($project['github']) && $project['github']['enabled'] == true )
            {
                $githubProjects[] = $this->projects->make($project['slug']);
                $choices[]        = $project['slug'];
            }
        }

        $choice = $this->choice('Pick the github enabled project you wish to sync', $choices);

        #$project    = $this->syncer->resolveProject($choice);
        $project = $choice;
        $tagsToSync = $this->syncer->getUnsyncedTags($project);
        $branchesToSync = $this->syncer->getUnsyncedBranches($project);
        $this->comment('Found ' . count($tagsToSync) . ' tags that needs to be synced in ' . $project);
        foreach ($tagsToSync as $tag)
        {
            $this->comment('Syncing ' . $tag['name']);
            $this->syncer->syncTag($project, $tag['name']);
            $this->info($tag['name'] . ' synced.');
        }
        $this->comment('Found ' . count($branchesToSync) . ' branches that needs to be synced');
        foreach($branchesToSync as $branch)
        {
            $this->comment("Syncing branch $project:$branch");
            $this->syncer->syncBranch($project, $branch);
            $this->info($branch . ' synced.');
        }
        $this->info('All done sire!');
    }

}
