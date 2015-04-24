<?php
/**
 * Part of the Laradic packages.
 * MIT License and copyright information bundled with this package in the LICENSE file.
 */
namespace Laradic\Docit\Http\Controllers;

use Alert;
use App;
use Debugger;
use GitHub;
use Illuminate\Contracts\Events\Dispatcher;
use Laradic\Docit\Contracts\ProjectFactory;
use Laradic\Docit\Projects\Project;
use Laradic\Support\Path;
use Redirect;
use Response;
use Symfony\Component\VarDumper\VarDumper;
use View;

/**
 * Class DocsController
 *
 * @package     Laradic\Docit\Http\Controllers
 * @author      Robin Radic
 * @license     MIT
 * @copyright   2011-2015, Robin Radic
 * @link        http://radic.mit-license.org
 */
class DocsController extends Controller
{

    public function index()
    {
        return Redirect::route('docit.project', [
            'project' => $this->projects[ 'default_project' ]
        ]);
    }

    public function show($project, $version = null, $pagePath = 'index')
    {
        $project = $this->projects->make($project);
        $version = isset($version) ? $version : $project->getDefaultVersion();

        $page       = $project->getPage($pagePath, $version);
        $content    = $page->getContent();
        $layout     = $page->getLayout();
        $attributes = $page->getAttributes();
        $menu       = $page->getMenu()->toArray();
        $projects   = $this->projects->all();
        $config     = $this->projects->getConfig();
        $view       = 'laradic/docit::page-layouts.' . $layout;

        $data = compact('project', 'version', 'page', 'content', 'layout', 'attributes', 'menu', 'projects', 'config', 'view');
        $this->events->fire('show');

        return View::make($view, $data);

    }
}
