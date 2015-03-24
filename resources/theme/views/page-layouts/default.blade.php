@extends('theme::layout')
@section('page-title')
    {{ $project['title'] }}
    @if(isset($project['subtitle']))
        <small>{{ $project['subtitle'] }}</small>
    @endif
@stop

@section('header-nav-menu')

    <li><a href="{{ URL::to('/') }}">Home</a></li>
    <li>
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Projects</a>
        <ul class="dropdown-menu dropdown-menu-wide">
            @foreach(Projects::all() as $proj)
                <li><a href="{{ $proj['url'] }}">{{ $proj['title'] }}</a></li>
            @endforeach
        </ul>
    </li>
@stop
@section('page-header.right')
    @if($project->isGithub())
    <div class="small-padding-top extra-large-margin-right pull-right">
        <div class="btn-toolbar" role="toolbar">
            <div class="btn-group" role="group">
                <a href="{{ $project->getGithubUrl() }}" class="btn btn-sm blue-light tipped" title="View on github" data-placement="bottom"  target="_blank"><i class="fa fa-git"></i></a>
                <a href="{{ $project->getGithubUrl() }}/archive/{{ $page->getVersion() }}.zip" class="btn btn-sm blue tipped" title="Download zip" data-placement="bottom" ><i class="fa fa-save"></i></a>
                @if(Config::get('app.debug'))
                <a href="{{ route('docit.github-sync-project', ['project' => $project['slug'] ]) }}" class="btn btn-sm blue-dark tipped" title="Synchronize project with github" data-placement="bottom" ><i class="fa fa-refresh"></i></a>
                @endif
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
    </style>
    @parent
@stop

@section('content')
    @if(isset($share_buttons))
        <div data-share-buttons="{{ join(',', $share_buttons) }}" data-share-buttons-class="share-btn-branded" class="group-share-btn-bottom"></div>
    @endif

    <div class="row page-layout-default">

        <div class="col-md-12">

            <div class="box">

              <header>
                  @if(isset($icon))
                      <i class="{{ $icon }}"></i>
                  @endif
                  <h3>{{ $title }}</h3>
              </header>

              <section>
                  <div class="blade-markdown">
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

@section('scripts.boot')
    @parent
    <script src="{{ Asset::url("laradic/docit::scripts/mdhighlight.js") }}"></script>
@stop
