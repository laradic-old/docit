<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Docit;

use App;
use Illuminate\Foundation\Application;
use Laradic\Docit\Log\Writer;
use Laradic\Docit\Parsers\MarkdownParser;
use Laradic\Docit\Projects\ProjectFactory;
use Laradic\Support\ServiceProvider;
use Monolog\Logger;


/**
 * This is the DocitServiceProvider class.
 *
 * @package        Laradic\Docit
 * @version        1.0.0
 * @author         Robin Radic
 * @license        MIT License
 * @copyright      2015, Robin Radic
 * @link           https://github.com/robinradic
 */
class DocitServiceProvider extends ServiceProvider
{

    /** @inheritdoc */
    protected $providers = [
        'Laradic\Themes\ThemeServiceProvider',
        'Laradic\Docit\Providers\RouteServiceProvider'
    ];

    public function provides()
    {
        return [ 'docit.parser', 'docit.projects', 'docit.github.sync' ];
    }

    /** @inheritdoc */
    public function boot()
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = parent::boot();

        $this->handleNavigation();
    }

    /**
     * Register the service provider.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function register()
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app    = parent::register();
        $config = $app->make('config')->get('laradic/docit::config');

        $this->registerLogging($config);
        $this->registerProjects($config);
        $this->registerParsers($config);
        $this->registerGithub($config);


        if ( $config[ 'console' ] )
        {
            $app->register('Laradic\Docit\Providers\ConsoleServiceProvider');
        }

        return $app;
    }

    protected function registerLogging($config)
    {
        $this->app->singleton('docit.log', function (Application $app) use ($config)
        {
            $monolog = new Logger('docit');
            $writer  = new Writer($monolog, $app->make('events'));
            if ( $config[ 'logging' ][ 'enabled' ] )
            {
                $writer->useFiles($config[ 'logging' ][ 'path' ]);
            }

            return $writer;
        });
        $this->app->bind('Laradic\Docit\Contracts\DocitLog', 'docit.log');
    }

    protected function registerProjects($config)
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = $this->app;

        $app->singleton('docit.projects', function (Application $app) use ($config)
        {
            return new ProjectFactory($app[ 'url' ], $config);
        });
        $app->bind('Laradic\Docit\Contracts\ProjectFactory', 'docit.projects');
        #$this->alias('docit.projects', 'Laradic\Docit\Contracts\ProjectFactory');

        $this->app->booting(function ()
        {
            $this->alias('Projects', 'Laradic\Docit\Facades\Projects');
        });
    }

    protected function registerParsers($config)
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = $this->app;

        $app->bind('docit.parsers.phpdoc', 'Laradic\Docit\Parsers\PhpdocParser');
        $app->bind('docit.parsers.markdown', function (Application $app)
        {
            return new MarkdownParser($app->make('markdown'), $app->make('config')->get('laradic/docit::markdown'));
        });
    }

    protected function registerGithub($config)
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = $this->app;

        if ( ! $config[ 'github' ][ 'enabled' ] )
        {
            return;
        }

        $app->register('GrahamCampbell\GitHub\GitHubServiceProvider');

        $app->bind('docit.github.sync', function (Application $app)
        {
            return new \Laradic\Docit\Github\GithubProjectSynchronizer(
                $gh = $app->make('GrahamCampbell\GitHub\GitHubManager'),
                $pf = $app->make('docit.projects'),
                $app->make('docit.log')
            );
        });
        $app->bind('Laradic\Docit\Contracts\ProjectSynchronizer', 'docit.github.sync');
    }

    protected function handleNavigation()
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = $this->app;

        $projects = $app->make('docit.projects')->all(true);

        $navigation = $app->make('navigation');
        $navigation->add('docit.header-left', 'Docit header left');
        $navigation->add('docit.header-left.projects', 'Projects', 'docit.header-left');
        foreach ( $projects as $project )
        {
            $navigation->add(
                'docit.header-left.projects.' . $project[ 'slug' ],
                $project[ 'title' ],
                'docit.header-left.projects',
                $project[ 'url' ]
            );
        }
    }
}
