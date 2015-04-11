@extends('theme::layout')

@section('title')
    {{ Config::get('laradic/docit::site_name') }}
@stop
@section('site-name')
    {{ Config::get('laradic/docit::site_name') }}
@stop

@section('links')
    <link rel="shortcut icon" href="{{ Asset::url("laradic/docit::favicon.ico") }}" type="image/x-icon">
    <link rel="icon" href="{{ Asset::url("laradic/docit::favicon.ico") }}" type="image/x-icon">
    @parent
@stop


@section('header-menu')
    @navigation('docit.header-left', 'theme::navigation.header-left')
@stop


@section('footer-copyright')
    DocIt Documentator &copy; {{ date("Y") }} <a href="http://radic.mit-license.org">Robin Radic</a> - <a href="http://radic.mit-license.org">MIT License</a>
@stop
