<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Docit\Commands;


use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Laradic\Docit\Contracts\ProjectSynchronizer;

/**
 * This is the DocitSyncProjectGithub class.
 *
 * @package        Laradic\Commands
 * @version        1.0.0
 * @author         Robin Radic
 * @license        MIT License
 * @copyright      2015, Robin Radic
 * @link           https://github.com/robinradic
 */
class DocitSyncProjectGithub implements SelfHandling, ShouldBeQueued
{

    use InteractsWithQueue, SerializesModels;

    /** @var \Laradic\Docit\Github\GithubProjectSynchronizer  */
    protected $syncer;

    protected $project;

    /**
     * Execute the command.
     *
     * @param \Laradic\Docit\Contracts\ProjectSynchronizer $syncer
     * @param                                              $project
     */
    public function handle(ProjectSynchronizer $syncer, $project)
    {
        $syncer->getLog()->info('Starting a project synchronization on the queue', func_get_args());
        $tagsToSync     = $syncer->getUnsyncedTags($project);
        $branchesToSync = $syncer->getUnsyncedBranches($project);
        foreach ( $tagsToSync as $tag )
        {
            $syncer->syncTag($project, $tag);
        }
        foreach ( $branchesToSync as $branch )
        {
            $syncer->syncBranch($project, $branch);
        }
        $syncer->getLog()->info('Finished a project synchronization on the queue');
    }

}
