<?php
return array(
    'site_name'               => 'DocIt',
    'base_route'              => 'doc',
    'projects_path'           => 'docs',
    'default_project'         => 'blade-extensions',
    'default_page_attributes' => array(
        'layout'            => 'default',
        'code_color_scheme' => 'zenburn',
        'disqus'            => false,
        'share_buttons'     => array('facebook', 'twitter', 'linkedin')
    ),
    'github'                  => array(
        'enabled'      => true,
        'token'        => env('GITHUB_TOKEN', null),
        'webhook_sync' => env('GITHUB_WEBHOOK_SYNC', null),
        'oauth_io'     => env('GITHUB_OAUTH_IO', null)
    ),
    'disqus'                  => array(
        'enabled'   => true,
        'shortname' => 'docit',
    ),
    'parser'                  => array(
        /* Tags can be added in the markdown file like:
         * <!---+ col-md-6 +-->
         * <!---+ /col-md-6 +-->
         * Add anything you'd like
         */


        'tags' => array(
            '(?<!\/)bs-material'             => '<div class="bs-material">',
            '\/bs-material'                  => '</div>',
            '(?<!\/)contextual:(.*?)'        => '<div style="padding:10px 10px 5px;" class="bg-$1">',
            '\/contextual'                   => '</div>',
            '(?<!\/)hide'                    => '<div class="hide">',
            '\/hide'                         => '</div>',
            '(?<!\/)col-md-(\d*)'            => '<div class="col-md-$1">',
            '\/col-md-(\d*)'                 => '</div>',
            '(?<!\/)row'                     => '<div class="row">',
            '\/row'                          => '</div>',
            'table(.*?)' => '<div class="table table-markdoc $1">',
            '\/table' => '</div>',
            'bar:(.*?):(\d*?):(\d*?):(\d*?)' => '<div class="progress"><div role="progressbar" aria-valuenow="$3" aria-valuemin="$2" aria-valuemax="$4"' .
                'style="width: $3%" class="progress-bar progress-bar-$1"><span class="sr-only">' .
                '$3%</span></div></div>'
        )
    ),
    'console'                 => true // Add console commands
);
