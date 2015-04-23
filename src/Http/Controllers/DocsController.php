<?php
/**
 * Part of the Laradic packages.
 * MIT License and copyright information bundled with this package in the LICENSE file.
 */
namespace Laradic\Docit\Http\Controllers;

use App;
use GitHub;
use Laradic\Docit\Parser;
use Redirect;
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
            'project' => $this->projects->getConfig()['default_project']
        ]);
    }

    public function show($project, $version = null, $page = 'index')
    {


        $project = $this->projects->make($project);
        $page    = $project->getPage($page, $version);

        $content = $page->getRenderedContent();

        $layout     = $page->getLayout();
        $attributes = $page->getAttributes();

        $attributes['menu']    = $page->getMenu()->toArray();
        $attributes['content'] = $content;
        $attributes['version'] = isset($version) ? $version : $project->getDefaultVersion();

        $attributes['project']  = $project;
        $attributes['page']     = $page;
        $attributes['projects'] = $this->projects->all();

        $attributes['config'] = $this->projects->getConfig();
        $this->events->fire('show');

        return View::make('laradic/docit::page-layouts.' . $layout)->with($attributes);
    }

    public function phpdoc()
    {

    }
}
