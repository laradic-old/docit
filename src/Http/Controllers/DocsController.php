<?php namespace Laradic\Docit\Http\Controllers;

use App;
use Config;
use GitHub;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Controller as BaseController;
use Projects;
use Redirect;
use Throttle;
use View;

#use App\Http\Controllers\Controller as BaseController;

class DocsController extends BaseController
{

    protected $app;

    function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function index()
    {
        return Redirect::route('docit.project', [
            'project' => Config::get('laradic_docit.default_project')
        ]);
    }

    public function show($project, $version = null, $page = 'index')
    {

        $project = Projects::make($project);
        $page    = $project->getPage($page, $version);

        $layout     = $page->getLayout();
        $attributes = $page->getAttributes();

        $attributes['menu']    = $page->getMenu()->toArray();
        $attributes['content'] = $page->getRenderedContent();
        $attributes['version'] = isset($version) ? $version : $project->getDefaultVersion();

        $attributes['project']  = $project;
        $attributes['page']     = $page;
        $attributes['projects'] = Projects::all();

        $attributes['config'] = Config::get('laradic_docit');
        $this->app->events->fire('show');

        return View::make('laradic/docit::page-layouts.' . $layout)->with($attributes);
    }
}
