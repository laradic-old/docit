<?php namespace Laradic\Docit\Console;

use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Laradic\Docit\Github\GithubProjectSynchronizer;
use Laradic\Docit\Parser;
use Laradic\Support\AbstractConsoleCommand;
use Laradic\Support\Arr;
use Laradic\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\VarDumper\VarDumper;

class TestCommand extends AbstractConsoleCommand
{

    protected $name = 'docs:test';

    protected $description = 'Command description.';


    public function fire()
    {
        $mdstr = file_get_contents(__DIR__ . '/../test.md');
        $p = new Parser();

        $parsedString = $p->parse($mdstr);

        VarDumper::dump($parsedString);
    }

}
