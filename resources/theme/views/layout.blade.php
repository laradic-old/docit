@extends('theme::layout')

@section('links')
    <link rel="shortcut icon" href="{{ Asset::url("laradic/docit::favicon.ico") }}" type="image/x-icon">
    <link rel="icon" href="{{ Asset::url("laradic/docit::favicon.ico") }}" type="image/x-icon">
    @parent
@stop

@section('header-nav-menu')
    <li><a href="{{ URL::to('/') }}">Home</a></li>
    <li>
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Projects</a>
        <ul class="dropdown-menu dropdown-menu-wide">
            @foreach(Projects::all() as $project)
                <li><a href="{{ $project['url'] }}">{{ $project['title'] }}</a></li>
            @endforeach
        </ul>
    </li>
@stop

@section('footer-copyright')
    DocIt Documentator &copy; {{ date("Y") }} <a href="http://radic.mit-license.org">Robin Radic</a> - <a href="http://radic.mit-license.org">MIT License</a>
@stop
