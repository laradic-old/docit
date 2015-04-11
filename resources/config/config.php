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
        'oauth_io' => env('GITHUB_OAUTH_IO', null)
    ),

    'disqus' => array(
        'enabled' => true,
        'shortname' => 'docit',
    ),

    'parser' => array(
        /* Tags can be added in the markdown file like:
         * <!---+ col-md-6 +-->
         * <!---+ /col-md-6 +-->
         * Add anything you'd like
         */

        'tags' => array(
            'col-md-6'   => '<div class="col-md-6">',
            '\/col-md-6' => '</div>',
            'row'        => '<div class="row">',
            '\/row'      => '</div>'
        )
    ),
    'console' => true // Add console commands
);
