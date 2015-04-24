@extends('laradic/docit::layout')

@section('title')
    @parent
    | {{ $project['title'] }}
@stop

@section('page-title')
    {{ $project['title'] }}
    @if(isset($project['subtitle']))
        <small>{{ $project['subtitle'] }}</small>
    @endif
@stop

@section('page-header.right')
    @if($project->isGithub())
        <div class="small-padding-top extra-large-margin-right pull-right">
        <div class="btn-toolbar" role="toolbar">
            <div class="btn-group" role="group">
                <a href="{{ $project->getGithubUrl() }}" class="btn btn-sm blue-light" data-toggle="tooltip" title="View on github" data-placement="bottom" target="_blank"><i class="fa fa-git"></i></a>
                <a href="{{ $project->getGithubUrl() }}/archive/{{ $page->getVersion() }}.zip" class="btn btn-sm blue" data-toggle="tooltip" title="Download zip" data-placement="bottom"><i class="fa fa-save"></i></a>
            </div>

            <div class="btn-group" role="group">
                @if(Config::get('app.debug'))
                    <a href="{{ route('docit.github-sync-project', ['project' => $project['slug'] ]) }}" class="btn btn-sm red-dark" data-toggle="tooltip" title="Synchronize project with github" data-placement="bottom"><i class="fa fa-refresh"></i></a>
                    <a href="#" id="clear-localstorage" class="btn btn-sm red" data-toggle="tooltip" title="Clear the LocalStorage" data-placement="bottom"><i class="fa fa-trash"></i></a>
                @endif

                <a href="#" id="github-edit-button" class="btn btn-sm orange-dark hide" data-toggle="tooltip" title="Edit this page" data-placement="bottom"><i class="fa fa-pencil"></i></a>
                <a href="#" id="github-edit-menu-button" class="btn btn-sm orange hide" data-toggle="tooltip" title="Edit the menu" data-placement="bottom"><i class="fa fa-list"></i></a>
                <a href="#" id="github-auth-button" class="btn btn-sm orange-light" data-toggle="tooltip" title="Login with Github (if you have the rights)" data-placement="bottom"><i class="fa fa-pencil"></i></a>
            </div>
            @include('laradic/docit::partials.project-version-picker', [ 'project' => $project, 'version' => $version ])
        </div>
    </div>
    @endif
@stop

@section('styles')
    <link href="{{ Asset::url('theme::scripts/plugins/highlightjs/styles/' . $code_color_scheme . '.css') }}" type="text/css" rel="stylesheet">
    <style type="text/css">
        .hljs {
            background: #3f3f3f;
            color: #dcdcdc;
        }
        .markdoc {
            display: none;
        }
    </style>
    @parent
@stop

@section('content')
    @if(isset($share_buttons))
        <div data-share-buttons="{{ join(',', $share_buttons) }}" data-share-buttons-class="share-btn-branded" class="group-share-btn-bottom"></div>
    @endif

    <div class="row page-layout-default">

        <div class="col-md-12">

            <div id="github-editor-container"></div>

            <div id="page-content-box" class="box">

              <header>
                  @if(isset($icon))
                      <i class="{{ $icon }}"></i>
                  @endif
                  <h3>{{ $title }}</h3>
              </header>

              <section>
                  <div class="blade-markdown markdoc">
                      <?php echo $content; ?>
                  </div>
              </section>

            </div>

            @if($disqus)
                @include('laradic/docit::partials.disqus')
            @endif
        </div>

    </div>


@stop

@section('scripts.custom')
    <script>
        (function(){
            var packadic = (window.packadic = window.packadic || {});
            packadic.mergeConfig({
                requireJS: {
                    paths: {
                        'docit/markdoc': '{{ Asset::url('laradic/docit::scripts/markdoc') }}'
                    }
                }
            });

            packadic.bindEventHandler('starting', function(){
                require([ 'jquery', 'docit/markdoc'], function( $, markdoc ){
                    markdoc.applyTo('#page-content-box .markdoc');
                });
            });
        }.call());
    </script>

    @if($project->isGithub() and !empty($project['github.branches']))
        <script>
            (function(){
                var packadic = (window.packadic = window.packadic || {});

                var owner = '{{ $project['github.username'] }}';
                var repoName = '{{ $project['github.repository'] }}';
                var path = '{{ $project['github.path_bindings.docs'] . '/' . $page->getPath() }}';
                var branch = '{{ $version }}';


                packadic.mergeConfig({
                    debug   : true,
                    oauth_io: '{{ Config::get('laradic/docit::github.oauth_io') }}'
                });

                packadic.bindEventHandler('starting', function(){
                    require([ 'jquery', 'docit/markdoc', 'github-editor', 'string' ], function( $, editor, _s ){
                        window.editor = editor;

                        $('#clear-localstorage').on('click', function( e ){
                            e.preventDefault();
                            localStorage.clear();
                            console.info('localStorage cleared');
                        });

                        var $githubEditorContainer = $('#github-editor-container'),
                            $githubAuthButton = $('#github-auth-button'),
                            $githubEditButton = $("#github-edit-button"),
                            $githubEditMenuButton = $("#github-edit-menu-button"),
                            $pageContentBox = $("#page-content-box");

                        editor.ui.getAuthButton($githubAuthButton);

                        if( !editor.github.isAuthorized() ){
                            return null;
                        }

                        var github = editor.github,
                            codepad = editor.codepad,
                            ui = editor.ui;

                        codepad.setContainer($githubEditorContainer);

                        $githubEditButton.removeClass('hide').on('click', function( e ){
                            e.preventDefault();
                            ui.createFileEditor(owner, repoName, branch, _s.endsWith(path, '.md') ? path : path + '.md')
                        });

                        $githubEditMenuButton.removeClass('hide').on('click', function( e ){
                            e.preventDefault();
                            ui.createFileEditor(owner, repoName, branch, '{{ $project['github.path_bindings.docs'] }}/menu.yml')
                        });

                        editor.on('editor.file.open', function(){
                            $pageContentBox.slideUp(500, function(){
                                codepad.slideDown(500, function(){
                                    codepad.toTopLine()
                                });
                            });
                        });
                        editor.on('editor.file.close', function(){
                            codepad.slideUp(500, function(){
                                $pageContentBox.slideDown(500);
                            });
                        });

                        //editor.showRepositories();
                        //editor.showFileEditor(editor.github.getRepo('robinradic', 'blade-extensions'), {}, 'develop', 'README.md');


                    })
                })
            }.call())
        </script>
    @endif
    @parent
    <script src="{{ Asset::url("laradic/docit::scripts/mdhighlight.js") }}"></script>
@stop
