<?php
/**
 * Part of the Radic packages.
 */
namespace Laradic\Docit\Projects;

#use Debugger;
use ArrayAccess;
use File;
use Illuminate\Routing\UrlGenerator;
use Laradic\Docit\Contracts\ProjectFactory as ProjectFactoryContract;
use Laradic\Support\Arrays;
use Laradic\Support\String;
use Laradic\Support\Traits\DotArrayAccessTrait;

/**
 * Class ProjectFactory
 *
 * @package     Laradic\Docit
 * @author      Robin Radic
 * @license     MIT
 * @copyright   2011-2015, Robin Radic
 * @link        http://radic.mit-license.org
 */
class ProjectFactory implements ProjectFactoryContract, ArrayAccess
{
    use DotArrayAccessTrait;

    protected function getArrayAccessor()
    {
        return 'config';
    }

    /**
     * @var Project[]
     */
    protected $projects;

    /**
     * The URL generator instance
     * @var \Illuminate\Routing\UrlGenerator
     */
    protected $url;

    /**
     * The configuration items
     * @var array
     */
    protected $config;

    /**
     * The absolute path to the root directory residing the project documentation
     * @var string
     */
    protected $projectsFilePath;

    /**
     * Instanciates the class
     *
     * @param \Illuminate\Routing\UrlGenerator $url
     * @param array                            $config
     */
    public function __construct(UrlGenerator $url, array $config)
    {
        $this->config           = $config;
        $this->url              = $url;
        $this->projectsFilePath = public_path($config['projects_path']);
        $this->projects         = $this->resolveAllProjects();
    }

    protected function resolveAllProjects()
    {
        $projects                = [];
        $projectsPath            = $this->path();
        $projectsConfigFilePaths = File::glob($projectsPath . '/*/project.php');
        foreach ($projectsConfigFilePaths as $configFilePath)
        {
            $slug = String::remove($configFilePath, $projectsPath);
            $slug = String::remove($slug, 'project.php');
            $slug = String::remove($slug, '/');

            $projects[$slug] = array_merge(require $configFilePath, [
                'slug' => $slug,
                'path' => String::remove($configFilePath, '/project.php'),
                'url'  => $this->url($slug)
            ]);

            $projects[$slug]['default_page_attributes'] = array_merge(
                isset($this->config['default_page_attributes']) ? $this->config['default_page_attributes'] : [],
                isset($projects[$slug]['default_page_attributes']) ? $projects[$slug]['default_page_attributes'] : []
            );
        }

        return $projects;
    }

    /**
     * all
     *
     * @param bool $bySlug
     * @return array|\Laradic\Docit\Project[]
     */
    public function all($bySlug = false)
    {
        return $bySlug === true ? $this->projects : Arrays::values($this->projects);
    }

    public function has($slug)
    {
        return isset($this->projects[$slug]);
    }

    public function get($slug)
    {
        return $this->projects[$slug];
    }

    public function slugs()
    {
        return Arrays::keys($this->projects);
    }

    public function make($slug)
    {
        $projectConfig = $this->projects[$slug];

        return new Project($this, $slug, $projectConfig);
    }

    public function path()
    {
        return $this->projectsFilePath;
    }

    public function url($project = null, $version = null, $page = null)
    {
        $uri = $this->config['base_route'];
        if ( ! is_null($project) )
        {
            if ( $project instanceof Project )
            {
                $uri .= '/' . $project->getSlug();
            }
            else
            {
                $uri .= '/' . $project;
            }

            if ( $version )
            {
                $uri .= '/' . $version;
                if ( $page )
                {
                    $uri .= '/' . $page;
                }
            }
        }

        return $this->url->to($uri);
    }

    /**
     * Get the value of config
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

}
