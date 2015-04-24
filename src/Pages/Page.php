<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Docit\Pages;

use ArrayAccess;
use Cache;
use Closure;
use File;
use Laradic\Docit\Projects\Menu;
use Laradic\Docit\Projects\Project;
use Laradic\Support\Path;
use Laradic\Support\Traits\DotArrayAccessTrait;
use stdClass;

/**
 * This is the Page class.
 *
 * @package        Laradic\Docit
 * @version        1.0.0
 * @author         Robin Radic
 * @license        MIT License
 * @copyright      2015, Robin Radic
 * @link           https://github.com/robinradic
 */
abstract class Page implements ArrayAccess
{
    use DotArrayAccessTrait;

    protected function getArrayAccessor()
    {
        return 'attributes';
    }

    /** @var array */
    protected $attributes;

    /** @var \Laradic\Docit\Projects\Project */
    protected $project;

    /** @var string */
    protected $page;

    /** @var string */
    protected $version;

    /** @var string */
    protected $content;

    /** @var \Laradic\Docit\Projects\Menu */
    protected $menu;


    /**
     * Instanciates the class
     *
     * @param \Laradic\Docit\Projects\Project $project
     * @param                                 $version
     * @param                                 $page
     */
    public function __construct(Project $project, $version, $page)
    {
        $this->project = $project;
        $this->version = $version;
        $this->page    = $page;
        $this->menu    = new Menu($this->project, $this->version);
        $this->make();
    }

    /** @return string */
    abstract protected function make();

    /**
     * Get a file
     *
     * @param          $filePath
     * @param callable $fileProcessor
     * @return stdClass
     */
    protected function getFile($filePath, Closure $fileProcessor = null)
    {
        $debug            = config('app.debug');
        $fileLastModified = File::lastModified($filePath);
        $fileId           = md5($filePath);

        $projectPath         = Path::join($this->project->getPath(), 'project.php');
        $projectLastModified = File::lastModified($projectPath);

        if ( ! $debug and Cache::has($fileId . '_lastModified') && Cache::has($fileId . '_project_lastModified') )
        {

            if ( $fileLastModified > Cache::get($fileId . '_lastModified') or $projectLastModified > Cache::get($fileId . '_project_lastModified') )
            {
                Cache::forget($fileId);
                Cache::forget($fileId . '_lastModified');
                Cache::forget($fileId . '_project_lastModified');
            }
            else
            {
                return Cache::get($fileId);
            }
        }

        $obj       = new stdClass();
        $obj->file = File::exists($filePath) ? File::get($filePath) : null;

        if ( isset($fileProcessor) and $fileProcessor instanceof Closure )
        {
            $obj = $fileProcessor($obj);
        }

        if ( ! $debug )
        {
            Cache::forever($fileId . '_lastModified', $fileLastModified);
            Cache::forever($fileId . '_project_lastModified', $projectLastModified);
            Cache::forever($fileId, $obj);
        }

        return $obj;
    }


    /**
     * get version value
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * get content value
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * get menu value
     *
     * @return Menu
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * getPage
     *
     * @return string
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * get project value
     *
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }



    /**
     * Get the path to this project/version root doc folder
     *
     * @return string
     */
    public function getPath()
    {
        return Path::join($this->project->getPath(), $this->version);
    }

    //
    /* ATTRIBUTE GETTERS */
    //
    public function hasAttribute($name)
    {
        return $this->offsetExists($name);
    }

    public function getAttribute($name)
    {
        return $this->offsetGet($name);
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttribute($name, $value)
    {
        $this->attributes[ $name ] = $value;

        return $this;
    }


    public function getLayout()
    {
        return $this->offsetGet('layout');
    }


}
