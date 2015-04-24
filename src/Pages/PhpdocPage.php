<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Docit\Pages;

use App;
use Exception;
use Laradic\Docit\Projects\Project;
use Laradic\Support\Path;
use Laradic\Support\String;
use stdClass;
use Symfony\Component\VarDumper\VarDumper;
use View;


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
class PhpdocPage extends Page
{

    /** @return string */
    protected function make()
    {

        $xml = $this->getFile('', function(stdClass $obj){

            $files = app('files');
            $project = $this->getProject();
            $xmlPath       = Path::join($this->getPath(), $project[ 'phpdoc.xml_path' ]);
            $outputDirPath = Path::join($this->getPath(), $project[ 'phpdoc.dir' ]);

            if ( ! $files->exists($xmlPath) )
            {
                throw new Exception("Could not parse PHPDoc. The XML file does not exist [$xmlPath]");
            }

            $obj->file = $files->get($xmlPath);
            $obj->tree = app('docit.parsers.phpdoc')->parse($obj->file);

            return $obj;
        });

        $this->attributes = $this->project->getDefaultPageAttributes();

        $viewData = [
            'tree' => $xml->tree,
            'menu' => $this->menu,
            'page' => String::removeLeft($this->project['phpdoc.dir'], $this->page)->__toString(),
            'attributes' => $this->attributes
        ];

        $index = View::make('laradic/docit::phpdoc.index')->with($viewData)->render();

        $viewData['index'] = $index;
        $this->content = View::make('laradic/docit::phpdoc.page')->with($viewData);

        VarDumper::dump($viewData);
        die();
    }

    protected function parse($str)
    {
        return app('docit.parsers.phpdoc')->parse($str);
    }
}
