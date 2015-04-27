<?php namespace Laradic\Docit\Console;


use Laradic\Support\Arrays;
use Laradic\Support\String;

class ListCommand extends BaseCommand
{

    protected $name = 'docit:list';

    protected $description = 'List all DocIt projects.';

    public function fire()
    {
        $rows = [ ];
        foreach ( $this->projects->all() as $project )
        {
            $p              = $this->projects->make($project[ 'slug' ]);
            $defaultVersion = $p->getDefaultVersion();
            $versions       = Arrays::replaceValue($p->getSortedVersions(), $defaultVersion, $this->colorize([ 'cyan', 'bold' ], $defaultVersion));
            $github         = isset($project[ 'github' ]) && $project[ 'github' ][ 'enabled' ] == true ? $project[ 'github' ][ 'username' ] . '/' . $project[ 'github' ][ 'repository' ] : $this->style([ 'red', 'bold' ], 'na');
            $path           = String::remove($project[ 'path' ], public_path() . '/');

            $rows[ ] = [ $project[ 'title' ], $project[ 'slug' ], $github, join(', ', $versions), $path ];
        }


        $this->table([ 'title', 'slug', 'github', 'versions', 'path' ], $rows);
        #Debugger::dump();
    }
}
