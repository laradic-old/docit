<?php
/**
 * Part of the Radic packages.
 */
namespace Laradic\Docit\Projects;

#use Debugger;
use File;
use Illuminate\Routing\UrlGenerator;
use Laradic\Docit\Contracts\ProjectFactory as ProjectFactoryContract;
use Laradic\Support\Arr;
use Laradic\Support\Str;

/**
 * Class ProjectFactory
 *
 * @package     Laradic\Docit
 * @author      Robin Radic
 * @license     MIT
 * @copyright   2011-2015, Robin Radic
 * @link        http://radic.mit-license.org
 */
class ProjectFactory implements ProjectFactoryContract
{

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
            $slug = Str::remove($configFilePath, $projectsPath);
            $slug = Str::remove($slug, 'project.php');
            $slug = Str::remove($slug, '/');

            $projects[$slug] = array_merge(require $configFilePath, [
                'slug' => $slug,
                'path' => Str::remove($configFilePath, '/project.php'),
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
        return $bySlug === true ? $this->projects : Arr::values($this->projects);
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
        return Arr::keys($this->projects);
    }

    public function make($slug)
    {
        $projectConfig = $this->projects[$slug];
        #Debugger::log($projectConfig);

        return new Project($this, $slug, $projectConfig);
    }

    public function path()
    {
        return $this->projectsFilePath;
        #return Themes::getActive()->getPackagesPath();
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
