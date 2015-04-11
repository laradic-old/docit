<?php namespace Laradic\Docit;

use App;
use Illuminate\Contracts\Foundation\Application;
use Laradic\Config\Traits\ConfigProviderTrait;
use Laradic\Docit\Projects\ProjectFactory;
use Laradic\Support\ServiceProvider;
use Laradic\Themes\Traits\ThemeProviderTrait;

class DocitServiceProvider extends ServiceProvider
{
    use ThemeProviderTrait, ConfigProviderTrait;

    public function boot()
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = parent::boot();

        # Theme package publisher
        $this->addPackagePublisher('laradic/docit', __DIR__ . '/../resources/theme');
    }

    /** @inheritdoc */
    public function register()
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = parent::register();

        $this->addConfigComponent('laradic/docit', 'laradic/docit', realpath(__DIR__ . '/../resources/config'));

        $config = $this->app['config']->get('laradic/docit::config');

        $app->singleton('docit.parser', function(Application $app) use ($config){
            return new Parser($app->make('markdown'), $config['parser']);
        });

        $this->app->register('Laradic\Themes\ThemeServiceProvider');
        $this->app->register('Laradic\Docit\Providers\RouteServiceProvider');
        $this->app->singleton('Laradic\Docit\Contracts\ProjectFactory', function ($app) use ($config)
        {
            return new ProjectFactory($app['url'], $config);
        });
        $this->alias('Projects', 'Laradic\Docit\Facades\Projects');

        # Optionals
        if ( $config['github']['enabled'] )
        {
            $this->registerGithub();
        }

        if ( $config['console'] )
        {
            $this->app->register('Laradic\Docit\Providers\ConsoleServiceProvider');
        }

        # Navigation
        $nav = $app->make('navigation');
        $nav->add('docit.header-left', 'Docit header left');
        $nav->add('docit.header-left.projects', 'Projects', 'docit.header-left');

        $projects = $app->make('Laradic\Docit\Contracts\ProjectFactory')->all(true);
        foreach ($projects as $project)
        {
            $nav->add(
                'docit.header-left.projects.' . $project['slug'],
                $project['title'],
                'docit.header-left.projects',
                $project['url']
            );
        }
    }

    protected function registerGithub()
    {
        $this->app->register('GrahamCampbell\GitHub\GitHubServiceProvider');

        $this->app->bind('Laradic\Docit\Github\GithubProjectSynchronizer', function (Application $app)
        {
            return new \Laradic\Docit\Github\GithubProjectSynchronizer(
                $gh = $app->make('GrahamCampbell\GitHub\GitHubManager'),
                $pf = $app->make('Laradic\Docit\Contracts\ProjectFactory')
            );
        });
    }
}
