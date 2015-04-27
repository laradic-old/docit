<?php namespace Laradic\Docit\Console;


use Laradic\Docit\Commands\DocitSyncProjectGithub;
use Laradic\Docit\Contracts\ProjectFactory;
use Laradic\Docit\Contracts\ProjectSynchronizer;
use Laradic\Docit\Github\GithubProjectSynchronizer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class GithubSyncCommand extends BaseCommand
{

    protected $name = 'docit:github-sync';

    protected $description = 'Synchronise all Github projects.';

    /** @var GithubProjectSynchronizer */
    protected $syncer;

    public function __construct(ProjectFactory $projects, ProjectSynchronizer $syncer)
    {
        parent::__construct($projects);
        $this->syncer = $syncer;
    }

    public function fire()
    {
        if ( ! $project = $this->argument('project') )
        {
            $githubProjects = [ ];
            $choices        = [ ];
            foreach ( $this->projects->all() as $project )
            {
                if ( isset($project[ 'github' ]) && $project[ 'github' ][ 'enabled' ] == true )
                {
                    $githubProjects[ ] = $this->projects->make($project[ 'slug' ]);
                    $choices[ ]        = $project[ 'slug' ];
                }
            }

            $choice  = $this->choice('Pick the github enabled project you wish to sync', $choices);
            $project = $choice;
        }
        if ( $this->option('queue') )
        {
            \Queue::push(new DocitSyncProjectGithub($this->syncer), $project);
            $this->info('Github sync command added to the queue');
        }
        else
        {
            $tagsToSync     = $this->syncer->getUnsyncedTags($project);
            $branchesToSync = $this->syncer->getUnsyncedBranches($project);
            $this->comment('Found ' . count($tagsToSync) . ' tags that needs to be synced in ' . $project);
            foreach ( $tagsToSync as $tag )
            {
                $this->comment('Syncing ' . $tag);
                $this->syncer->syncTag($project, $tag);
                $this->info($tag . ' synced.');
            }
            $this->comment('Found ' . count($branchesToSync) . ' branches that needs to be synced');
            foreach ( $branchesToSync as $branch )
            {
                $this->comment("Syncing branch $project:$branch");
                $this->syncer->syncBranch($project, $branch);
                $this->info($branch . ' synced.');
            }
            $this->info('All done sire!');
        }
    }

    public function getOptions()
    {
        return [
            [ 'queue', 'Q', InputOption::VALUE_OPTIONAL, 'The stuff' ]
        ];
    }

    public function getArguments()
    {
        return [
            [ 'project', InputArgument::OPTIONAL, 'The project you want to sync' ]
        ];
    }
}
