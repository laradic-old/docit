<?php namespace Laradic\Docit\Providers;

use Laradic\Console\AggregateConsoleProvider;

class ConsoleServiceProvider extends AggregateConsoleProvider
{

    protected $namespace = 'Laradic\Docit\Console';

    protected $commands = [
        'CreateProject' => 'commands.laradic.docs.create.project',
        'GithubSync'    => 'commands.laradic.docs.github.sync',
        'List'          => 'commands.laradic.docs.list',
        'Test'          => 'commands.laradic.docs.test',
        'Log'           => 'commands.laradic.docs.log'
    ];

}
