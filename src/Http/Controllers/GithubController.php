<?php namespace Laradic\Docit\Http\Controllers;

#use App\Http\Controllers\Controller as BaseController;
use File;
use GitHub;
use GrahamCampbell\GitHub\GitHubManager;
use Illuminate\Routing\Controller as BaseController;
use Input;
use Laradic\Docit\Contracts\ProjectFactory;
use Laradic\Docit\Github\GithubProjectSynchronizer;
use Log;
use Request;
use Response;
use Symfony\Component\VarDumper\VarDumper;

class GithubController extends BaseController
{

    protected $github;

    protected $githubSync;

    /**
     * @var \Laradic\Docit\ProjectFactory
     */
    protected $projects;

    function __construct(GitHubManager $github, GithubProjectSynchronizer $githubSync, ProjectFactory $projects)
    {
        $this->github     = $github;
        $this->githubSync = $githubSync;
        $this->projects = $projects;
    }

    public function sync($project)
    {
        $this->githubSync->sync($project);
        $log = $this->githubSync->getLogEntries(true);
        VarDumper::dump($log);
    }

    public function webhook($type)
    {
        $types = ['push'];
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

        foreach($this->projects->all() as $project)
        {
            if(isset($project['github']) && isset($project['github']['enabled']) && $project['github']['enabled'] == true)
            {
                if($project['github']['username'] . '/' . $project['github']['repository'] === strtolower($payload['repository']['full_name']))
                {
                    $hash = hash_hmac('sha1', file_get_contents("php://input"), $project['github']['webhook_secret']);
                    if($headers['signature'] === "sha1=$hash")
                    {
                        Log::info('github webhook received push event. Syncing ' . $project['slug']);
                        $this->githubSync->sync($project['slug']);
                        Log::info('github webhook received push event. Synced ' . $project['slug'], $this->githubSync->getLogEntries(true));
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