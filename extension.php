<?php

use Illuminate\Foundation\Application;
use Laradic\Extensions\Extension;
use Laradic\Extensions\ExtensionFactory;

return array(
    'name' => 'DocIt',
    'slug' => 'laradic/docit',
    'dependencies' => [
        'laradic/packadic'
    ],
    'register' => function(Application $app, Extension $extension, ExtensionFactory $extensions)
    {
        $app->register('Laradic\Docit\DocitServiceProvider');
    },
    'boot' => function(Application $app, Extension $extension, ExtensionFactory $extensions)
    {
    },
    'install' => function(Application $app, Extension $extension, ExtensionFactory $extensions)
    {

    },
    'uninstall' => function(Application $app, Extension $extension, ExtensionFactory $extensions)
    {

    }
);
