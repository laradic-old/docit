<?php namespace Laradic\Docit\Console;

class TestCommand extends BaseCommand
{

    protected $name = 'docit:test';

    protected $description = 'Command description.';


    public function fire()
    {
        $mdstr = file_get_contents(__DIR__ . '/../test.md');
    }
}
