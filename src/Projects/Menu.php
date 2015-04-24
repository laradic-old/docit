<?php
/**
 * Part of the Radic packages.
 */
namespace Laradic\Docit\Projects;


use Exception;
use File;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Laradic\Support\Path;
use Laradic\Support\Str;
use Symfony\Component\Yaml\Yaml;
/**
 * Class Menu
 *
 * @package     Laradic\Docit
 * @author      Robin Radic
 * @license     MIT
 * @copyright   2011-2015, Robin Radic
 * @link        http://radic.mit-license.org
 */
class Menu implements Jsonable, Arrayable
{

    protected $project;

    protected $version;

    protected $filePath;

    protected $raw;

    protected $menu;


    /**
     * Instanciates the class
     *
     * @param \Laradic\Docit\Projects\Project $project
     * @param                                 $version
     * @throws \Exception
     */
    public function __construct(Project $project, $version)
    {
        $this->project  = $project;
        $this->version  = $version;
        $this->filePath = Path::join($project->getPath(), $version, 'menu.yml');

        if ( ! File::exists($this->filePath) )
        {
            throw new Exception("Could not find the menu.yml file for {$project->getPath()}");
        }

        $this->raw  = File::get($this->filePath);
        $this->menu = $this->parse($this->raw);
    }

    protected function parseConfig($str)
    {
        foreach(array_dot($this->project->getConfig()) as $key => $value)
        {
            $str = str_replace('${project.' . $key . '}', $value, $str);
        }
        return $str;
    }

    protected function parse($yaml)
    {
        $array = Yaml::parse($yaml);

        return $this->resolveMenu($array['menu']);
    }

    protected function resolveMenu($items)
    {
        $menu = [];
        foreach ($items as $key => $val)
        {
            $key = $this->parseConfig($key);
            $val = $this->parseConfig($val);

            # Key = title, val = relative page path
            if ( is_string($key) && is_string($val) )
            {
                $menu[] = [
                    'name' => $key,
                    'href' => $this->resolveLink($val)
                ];
            }
            elseif ( is_string($key) && $key === 'children' && is_array($val) )
            {
                $menu[] = $this->resolveMenu($val);
            }
            elseif ( isset($val['name']) )
            {
                $item = [
                    'name' => $val['name']
                ];
                if ( isset($val['href']) )
                {
                    $item['href'] = $this->resolveLink($val['href']);
                }
                elseif ( isset($val['page']) )
                {
                    $item['href'] = $this->resolveLink($val['page']);
                }
                if ( isset($val['icon']) )
                {
                    $item['icon'] = $val['icon'];
                }
                if ( isset($val['children']) && is_array($val['children']) )
                {
                    $item['children'] = $this->resolveMenu($val['children']);
                }
                $menu[] = $item;
            }
        }

        return $menu;
    }

    protected function resolveLink($val)
    {
        if ( Str::startsWith('http', $val) )
        {
            return $val;
        }
        else
        {
            $path = Str::endsWith($val, '.md') ? Str::remove($val, '.md') : $val;

            return $this->project->getProjects()->url($this->project, $this->version, $path);
        }
    }


    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->menu;
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->menu, $options);
    }
}
