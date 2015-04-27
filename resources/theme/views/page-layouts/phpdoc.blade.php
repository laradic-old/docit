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
@stop

@section('styles')
    {!! Asset::style('theme::styles/nav.css') !!}
    <link href="{{ Asset::url('theme::scripts/plugins/highlightjs/styles/' . ( $code_color_scheme ? $code_color_scheme : 'zenburn' ). '.css') }}" type="text/css" rel="stylesheet">
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

    <div class="page-layout-phpdoc">
        {!! $content !!}
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


        }.call());
    </script>
@stop
