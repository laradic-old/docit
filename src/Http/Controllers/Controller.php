<?php
/**
 * Part of the Laradic packages.
 * MIT License and copyright information bundled with this package in the LICENSE file.
 */
namespace Laradic\Docit\Http\Controllers;

use App;
use GitHub;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Routing\Controller as BaseController;
use Laradic\Docit\Contracts\ProjectFactory;

/**
 * Class Controller
 *
 * @package     Laradic\Docit\Http\Controllers
 * @author      Robin Radic
 * @license     MIT
 * @copyright   2011-2015, Robin Radic
 * @link        http://radic.mit-license.org
 */
class Controller extends BaseController
{

    /**
     * @var \Laradic\Docit\Projects\ProjectFactory
     */
    protected $projects;

    /**
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    public function __construct(ProjectFactory $projects, Dispatcher $events)
    {
        $this->projects = $projects;
        $this->events   = $events;
    }
}
