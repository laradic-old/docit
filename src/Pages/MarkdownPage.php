<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Docit\Pages;

use App;
use Laradic\Docit\Projects\Project;
use Laradic\Support\Path;
use Symfony\Component\Yaml\Yaml;


/**
 * This is the PhpdocPage class.
 *
 * @package        Laradic\Docit
 * @version        1.0.0
 * @author         Robin Radic
 * @license        MIT License
 * @copyright      2015, Robin Radic
 * @link           https://github.com/robinradic
 */
class MarkdownPage extends Page
{
    /**
     * {@inheritDoc}
     */
    protected function make()
    {
        $data = $this->getFile($this->getFilePath(), function (\stdClass $obj)
        {
            $obj->attributes = array_merge($this->project->getDefaultPageAttributes(), $this->parseAttributes($obj->file));
            $obj->content    = $this->parse($obj->file);

            return $obj;
        });

        $this->attributes = $data->attributes;
        $this->content    = $data->content;
    }

    /**
     * getFilePath
     *
     * @return string
     */
    public function getFilePath()
    {
        $path = stristr($this->page, '.md') ? $this->page : $this->page . '.md';

        return Path::join($this->project->getPath(), $this->version, $path);
    }

    /**
     * parseAttributes
     *
     * @param $str
     * @return array
     */
    protected function parseAttributes($str)
    {
        $pattern = '/<!---\n([\w\W]*?)\n-->/';
        preg_match($pattern, $str, $matches);
        if ( count($matches) > 1 )
        {
            return Yaml::parse($matches[ 1 ]);
        }

        return [ ];
    }

    /** @return string */
    public function parse($str)
    {
        return app('docit.parsers.markdown')->parse($str);
    }
}
