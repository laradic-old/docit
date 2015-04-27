<?php
/**
 * Part of the Laradic packages.
 * MIT License and copyright information bundled with this package in the LICENSE file.
 */
namespace Laradic\Docit\Http\Controllers;


use GitHub;
use Illuminate\Routing\Controller as BaseController;
use Input;
use Laradic\Docit\Contracts\DocitLog;
use Laradic\Docit\Contracts\ProjectFactory;
use Laradic\Docit\Contracts\ProjectSynchronizer;
use League\OAuth2\Client\Provider\Github as GithubProvider;
use Log;
use Request;
use Response;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Class GithubController
 *
 * @package     Laradic\Docit\Http\Controllers
 * @author      Robin Radic
 * @license     MIT
 * @copyright   2011-2015, Robin Radic
 * @link        http://radic.mit-license.org
 */
class GithubController extends BaseController
{

    protected $github;

    /**
     * @var \Laradic\Docit\Github\GithubProjectSynchronizer
     */
    protected $githubSync;

    protected $log;

    /**
     * @var \Laradic\Docit\Projects\ProjectFactory
     */
    protected $projects;

    public function __construct(ProjectSynchronizer $githubSync, ProjectFactory $projects, DocitLog $log)
    {
        $this->githubSync = $githubSync;
        $this->projects   = $projects;
        $this->log        = $log;
    }

    public function sync($project)
    {
        $this->githubSync->sync($project);
        $logEntries = $this->log->getLogEntries(true);
        VarDumper::dump($logEntries);
    }

    protected function getProvider($projectSlug)
    {
        $project = $this->projects->get($projectSlug);
        if ( ! $project->isGithub() )
        {
            throw new \Exception("Project [$projectSlug] is not a github project");
        }

        return new GithubProvider([
            'clientId'     => $project[ 'github.clientId' ],
            'clientSecret' => $project[ 'github.clientSecret' ],
            'redirectUri'  => \Config::get('laradic/docit::github.redirectUri'),
            'scopes'       => [ 'email', '...', '...' ],
        ]);
    }

    public function webhook($type)
    {
        $types = [ 'push' ];
        if ( ! in_array($type, $types) )
        {
            return Response::make('', 403);
        }

        $headers = [
            'delivery'   => Request::header('x-github-delivery'),
            'event'      => Request::header('x-github-event'),
            'user-agent' => Request::header('user-agent'),
            'signature'  => Request::header('x-hub-signature')
        ];
        $payload = Input::all();


$i = 0;
        foreach ( $this->projects->all() as $project )
        {
            $i++;
            if ( isset($project[ 'github' ][ 'enabled' ]) and $project[ 'github' ][ 'enabled' ] == true )
            {
                if ( $project[ 'github' ][ 'username' ] . '/' . $project[ 'github' ][ 'repository' ] === strtolower($payload[ 'repository' ][ 'full_name' ]) )
                {
                    $hash = hash_hmac('sha1', file_get_contents("php://input"), $project[ 'github' ][ 'webhook_secret' ]);
                    if ( $headers[ 'signature' ] === "sha1=$hash" )
                    {
                        $this->log->info('github webhook received push event. Syncing ' . $project[ 'slug' ]);
                        $this->githubSync->sync($project[ 'slug' ]);
                        $this->log->info('github webhook received push event. Synced ' . $project[ 'slug' ]);

                        return Response::make();
                    }
                    else
                    {
                        return Response::make('Invalid hash', 403);
                    }
                }
            }
        }

        Log::error('Github webhook received push event for unkown project', $payload);

        return Response::make('', 500);
    }
}
