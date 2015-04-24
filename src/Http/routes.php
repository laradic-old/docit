<?php

Route::get('/', ['as' => 'docit.home', 'uses' => 'DocsController@index']);

Route::get('/phpdoc', ['as' => 'docit.phpdoc', 'uses' => 'DocsController@phpdocApi']);


if(Config::get('laradic/docit::github.enabled') === true)
{
    if ( Config::get('app.debug') === true )
    {
        Route::get('github-sync/{project}', [
            'as'     => 'docit.github-sync-project',
            'before' => 'throttle:50,30',
            'uses'   => 'GithubController@sync'
        ]);
    }


    Route::any('github-sync-webhook/{type}', [
        'as'     => 'docit.github-sync-webhook',
        #'before' => 'throttle',
        'before' => 'throttle:50,30',
        'uses'   => 'GithubController@webhook'
    ]);
}


Route::get('{project}/{version?}/{pagePath?}', [
    'as' => 'docit.project', 'uses' => 'DocsController@show'
])->where('page', '(.*)');
