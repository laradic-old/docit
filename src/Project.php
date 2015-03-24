<?php
/**
 * Part of the Radic packages.
 */
namespace Laradic\Docit;


use Exception;
use File;
use Laradic\Support\Arr;
use Laradic\Support\Str;
use Naneau\SemVer\Sort;
use Stringy\Stringy;
use Symfony\Component\Yaml\Yaml;

/**
 * Class Project
 *
 * @package     Laradic\Docit
 * @author      Robin Radic
 * @license     MIT
 * @copyright   2011-2015, Robin Radic
 * @link        http://radic.mit-license.org
 */
class Project implements \ArrayAccess
{

    protected $slug;

    protected $projects;

    protected $path;

    protected $versions;

    protected $config;

    /**
     * Instanciates the class
     */
    public function __construct(ProjectFactory $projects, $slug, array $config)
    {
        $this->projects = $projects;
        $this->slug     = $slug;
        $config['slug'] = $slug;
        $this->config   = $config;
        $this->path     = $config['path'];

        $this->versions = $this->resolveVersions();

        #Debugger::tracy('project', $this);
    }

    protected function resolveVersions()
    {
        $versions = [];
        foreach (File::directories($this->path) as $directory)
        {
            $dirName            = Str::remove($directory, $this->path . '/');
            $versions[$dirName] = $directory;
        }

        return $versions;
    }

    public function getVersions($excludePaths = false)
    {
        return $excludePaths === true ? Arr::keys($this->versions) : $this->versions;
    }

    public function getSortedVersions($mode = 'desc')
    {
        $versions = $this->getVersions(true);
        if ( $mode == 'desc' )
        {
            arsort($versions, SORT_NUMERIC);
        }
        else
        {
            asort($versions, SORT_NUMERIC);
        }

        if ( $this->isGithub() && $this->hasGithubBranches() )
        {
            $versions = Arr::without($versions, $this['github']['branches']);
            $versions = array_merge($this['github']['branches'], $versions);
        }

        return $versions;
    }

    public function getDefaultVersion()
    {
        # hard configured in project.php
        if ( isset($this->config['default_version']) )
        {
            return $this->config['default_version'];
        }

        # github project? master branch..
        if ( $this->isGithub() && is_array($this['github']['branches']) && in_array('master', $this['github']['branches']) )
        {
            return 'master';
        }

        # last version otherwise
        $versions = [];
        foreach ($this->getVersions(true) as $shortVersion)
        {
            $versions[] = $shortVersion . '.0';
        }
        $version     = Arr::last(Sort::sort($versions))->getOriginalVersion();
        $lastVersion = Stringy::create($version)->removeRight('.0')->__toString();

        return $lastVersion;
    }

    public function getPage($path, $version = null)
    {
        if ( $version == null )
        {
            $version = $this->getDefaultVersion();
        }

        if ( ! isset($this->versions[$version]) )
        {
            throw new Exception("Version $version does not exist for this project");
        }

        return new Page($this, $version, $path);
    }


    public function getUrl()
    {
        return $this->projects->url($this, $this->getDefaultVersion());
    }

    public function getDefaultPageAttributes()
    {
        return $this['default_page_attributes'];
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getConfig()
    {
        return $this->config;
    }


    public function getSlug()
    {
        return $this->slug;
    }

    public function getProjects()
    {
        return $this->projects;
    }

    public function getTitle()
    {
        return $this['title'];
    }

    public function isGithub()
    {
        return (isset($this['github']) AND isset($this['github']['enabled']) AND $this['github']['enabled'] === true);
    }

    public function hasGithubBranches()
    {
        return ($this->isGithub() AND isset($this['github']['branches']) AND is_array($this['github']['branches']));
    }

    public function getGithubUrl()
    {
        return $this->isGithub() ? 'https://github.com/' . $this['github']['username'] . '/' . $this['github']['repository'] : '#';
    }


    /**
     * Determine if an item exists at an offset.
     *
     * @param  mixed $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->config);
    }

    /**
     * Get an item at a given offset.
     *
     * @param  mixed $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->config[$key];
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
            $this->config[] = $value;
        }
        else
        {
            $this->config[$key] = $value;
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
        unset($this->config[$key]);
    }

    public function toString()
    {
        return $this->slug;
    }
}
