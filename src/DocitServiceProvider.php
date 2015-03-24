<?php namespace Laradic\Docit;

use App;
use Illuminate\Contracts\Foundation\Application;
use Laradic\Support\ServiceProvider;
use Laradic\Themes\Traits\ThemeProviderTrait;
use View;

class DocitServiceProvider extends ServiceProvider
{
    use ThemeProviderTrait;

    protected $configFiles = ['laradic_docit'];

    protected $resourcesPath = '/../resources';

    protected $dir = __DIR__;

    public function boot()
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = parent::boot();

        $this->addPackagePublisher('laradic/docit', __DIR__ . '/../resources/theme');
    }

    /** @inheritdoc */
    public function register()
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = parent::register();

        $config = $this->app['config']->get('laradic_docit');

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
