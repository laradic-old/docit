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
use Laradic\Docit\Parsers\Phpdoc\File;
use Laradic\Support\Path;
use Laradic\Support\String;
use stdClass;
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

        $xml = $this->getFile('', function (stdClass $obj)
        {

            $files         = app('files');
            $project       = $this->getProject();
            $xmlPath       = Path::join($this->getPath(), $project[ 'phpdoc.dir' ], 'structure.xml');

            if ( ! $files->exists($xmlPath) )
            {
                throw new Exception("Could not parse PHPDoc. The XML file does not exist [$xmlPath]");
            }

            $obj->file = $files->get($xmlPath);
            $obj->tree = $this->parse($obj->file);

            return $obj;
        });

        # set default attributes & layout
        $this->attributes             = $this->project->getDefaultPageAttributes();
        $this->attributes[ 'layout' ] = $this->project[ 'phpdoc.layout' ];

        # adjust page to remove the phpdoc.dir at the start
        $page = String::removeLeft($this->page, $this->project[ 'phpdoc.dir' ]);
        $page = String::removeLeft($page, '/');

        # get the right phpdoc document file
        $segments = explode('/', $page);
        $doc      = $xml->tree;
        foreach ( $segments as $key => $item )
        {
            if ( isset($doc[ $item ]) )
            {
                $doc = $doc[ $item ];
            }
            else
            {
                foreach ( $doc as $d )
                {
                    if ( $d instanceof File and $d->name === $item )
                    {
                        $doc = $d;
                        break;
                    }
                }
            }
        }

        # setup view data
        $viewData = [
            'tree'        => $xml->tree,
            'menu'        => $this->menu,
            'page'        => $this,
            'currentPage' => String::removeLeft($this->page, $this->project[ 'phpdoc.dir' ]),
            'project'     => $this->project,
            'version'     => $this->version,
            'attributes'  => $this->attributes,
            'doc'         => $doc
        ];

        # render the index (phpdoc navigation) into index var, to use in the content
        $index               = View::make('laradic/docit::phpdoc.index')->with($viewData)->render();
        $viewData[ 'index' ] = $index;

        # and finally the page itself
        $this->content = View::make('laradic/docit::phpdoc.page')->with($viewData);
    }

    protected function parse($str)
    {
        return app('docit.parsers.phpdoc')->parse($str);
    }

    public function docUrl(File $docFile)
    {
        $page = String::create($docFile->fullName)->removeLeft('\\')->replace('\\', '/')->__toString();
        return $this->project->getProjects()->url($this->project->getSlug(), $this->version, $this->project['phpdoc.dir'] . '/' . $page);
    }
}
