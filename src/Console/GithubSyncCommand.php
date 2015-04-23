<?php namespace Laradic\Docit\Console;

use Illuminate\Foundation\Application;
use Laradic\Docit\Contracts\ProjectFactory;
use Laradic\Docit\Contracts\ProjectSynchronizer;
use Laradic\Docit\Github\GithubProjectSynchronizer;

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

        $choice = $this->choice('Pick the github enabled project you wish to sync', $choices);

        #$project    = $this->syncer->resolveProject($choice);
        $project        = $choice;
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
