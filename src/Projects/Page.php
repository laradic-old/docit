<?php
/**
 * Part of the Radic packages.
 */
namespace Laradic\Docit\Projects;

use ArrayAccess;
use Cache;
use File;
use Laradic\Support\Path;
use Symfony\Component\Yaml\Yaml;

/**
 * Class Page
 *
 * @package     Laradic\Docit
 * @author      Robin Radic
 * @license     MIT
 * @copyright   2011-2015, Robin Radic
 * @link        http://radic.mit-license.org
 */
class Page implements ArrayAccess
{

    protected $project;

    protected $attributes;

    protected $path;

    protected $filePath;

    protected $version;

    protected $renderedContent;

    protected $rawContent;

    protected $menu;


    /**
     * Instanciates the class
     */
    public function __construct(Project $project, $version, $path)
    {
        $this->project  = $project;
        $this->version  = $version;
        $this->path     = $path;
        $this->filePath = $this->getFilePath();
        list($this->rawContent, $this->attributes, $this->renderedContent) = $this->getPage();
        $this->menu = new Menu($this->project, $this->version);
    }

    protected function getPage()
    {

        $filePath         = $this->getFilePath();
        $fileLastModified = File::lastModified($filePath);
        $fileId           = md5($filePath);

        $projectPath         = Path::join($this->project->getPath(), 'project.php');
        $projectLastModified = File::lastModified($projectPath);

        if ( Cache::has($fileId . '_lastModified') && Cache::has($fileId . '_project_lastModified') )
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

        $raw = File::get($this->filePath);

        $page = [
            $raw,
            array_merge($this->project->getDefaultPageAttributes(), $this->parseAttributes($raw)),
            $this->parse($raw)
        ];

        Cache::forever($fileId . '_lastModified', $fileLastModified);
        Cache::forever($fileId . '_project_lastModified', $projectLastModified);
        Cache::forever($fileId, $page);


        return $page;
    }

    public function getFilePath()
    {
        $path = stristr($this->path, '.md') ? $this->path : $this->path . '.md';

        return Path::join($this->project->getPath(), $this->version, $path);
    }

    protected function parseAttributes($str)
    {
        $pattern = '/<!---\n([\w\W]*?)\n-->/';
        preg_match($pattern, $str, $matches);
        if ( count($matches) > 1 )
        {
            return Yaml::parse($matches[1]);
        }

        return [];
    }

    /** @return string */
    public function parse($str)
    {
        return app('docit.parser')->parse($str);
    }

    public function getVersion()
    {
        return $this->version;
    }


    public function getRawContent()
    {
        return $this->rawContent;
    }

    public function getRenderedContent()
    {
        return $this->renderedContent;
    }


    public function getMenu()
    {
        return $this->menu;
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


    public function getLayout()
    {
        return $this->offsetGet('layout');
    }


    /**
     * Determine if an item exists at an offset.
     *
     * @param  mixed $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * Get an item at a given offset.
     *
     * @param  mixed $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->attributes[$key];
    }

    /**
     * Set the item at a given offset.
     *
     * @param  mixed $key
     * @param  mixed $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        if ( is_null($key) )
        {
            $this->attributes[] = $value;
        }
        else
        {
            $this->attributes[$key] = $value;
        }
    }

    /**
     * Unset the item at a given offset.
     *
     * @param  string $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->attributes[$key]);
    }

    /**
     * Get the value of path
     *
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }
}
