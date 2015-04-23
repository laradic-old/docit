<?php namespace Laradic\Docit\Providers;

use Laradic\Support\AbstractConsoleProvider;

class ConsoleServiceProvider extends AbstractConsoleProvider
{

    protected $namespace = 'Laradic\Docit\Console';

    protected $commands = [
        'CreateProject'  => 'commands.laradic.docs.create.project',
        'GithubSync'     => 'commands.laradic.docs.github.sync',
        'List'           => 'commands.laradic.docs.list',
        'Test'           => 'commands.laradic.docs.test',
        'PhpdocGenerate' => 'commands.laradic.docs.phpdoc'
    ];

    //GithubSync
}
