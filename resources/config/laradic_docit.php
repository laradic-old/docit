<?php
return array(
    'site_name' => 'DocIt',
    'base_route' => 'doc',
    'projects_path' => 'docs',
    'default_project' => 'blade-extensions',

    'default_page_attributes' => array(
        'layout' => 'default',
        'code_color_scheme' => 'zenburn',
        'disqus' => false,
        'share_buttons' => array('facebook', 'twitter', 'linkedin')
    ),

    'github' => array(
        'enabled' => true,
        'token' => env('GITHUB_TOKEN', null),
        'webhook_sync' => env('GITHUB_WEBHOOK_SYNC', null),
    ),

    'disqus' => array(
        'enabled' => true,
        'shortname' => 'docit',

    ),

    'header_menu' => array(
        'Home' => 'home',
        'Projects' => '#project-list#',
        'Test actions' => array(
            'Throttle' => 'throttle'
        )
    ),
    'console' => true // Add console commands
);
