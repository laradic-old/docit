<?php namespace Laradic\Docit\Console;

use Laradic\Docit\Contracts\ProjectFactory;
use Laradic\Docit\Phpdoc\PhpdocMD;
use Laradic\Support\Filesystem;
use Laradic\Support\Path;

class PhpdocGenerateCommand extends BaseCommand
{

    protected $name = 'docit:phpdoc';

    protected $description = 'Generate PHPDoc for all enabled projects.';

    /** @var \Laradic\Support\Filesystem */
    protected $files;

    public function __construct(ProjectFactory $projects, Filesystem $files)
    {
        parent::__construct($projects);
        $this->files = $files;
    }


    public function fire()
    {
        foreach ( $this->projects->all() as $project )
        {
            if ( isset($project[ 'phpdoc' ]) and $project[ 'phpdoc'][ 'enabled' ] === true )
            {
                $project = $this->projects->make($project['slug']);

                foreach ( $project->getVersions() as $version => $versionPath )
                {
                    $xmlPath       = Path::join($versionPath, $project[ 'phpdoc.xml_path' ]);
                    $outputDirPath = Path::join($versionPath, $project[ 'phpdoc.output_dir' ]);

                    if ( ! $this->files->exists($xmlPath) )
                    {
                        continue;
                    }

                    if ( ! $this->files->isDirectory($outputDirPath) )
                    {
                        $this->files->makeDirectory($outputDirPath, 0755, true);
                    }

                    PhpdocMD::make($xmlPath)->generate($outputDirPath);
                    $this->info('Generated PHPDoc Markdown for ' . $project->getSlug() . '@' . $version);
                }
            }
        }
        $this->info($this->name . ' has been executed');
    }
}
