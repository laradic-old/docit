![Laravel logo](http://laravel.com/assets/img/laravel-logo.png) DocIt documentation extension
============================

[![GitHub Version](https://img.shields.io/github/tag/laradic/docit.svg?style=flat-square&label=version)](http://badge.fury.io/gh/laradic%2Fdocit)
[![Total Downloads](https://img.shields.io/packagist/dt/laradic/docit.svg?style=flat-square)](https://packagist.org/packages/laradic/docit)
[![License](http://img.shields.io/badge/license-MIT-ff69b4.svg?style=flat-square)](http://radic.mit-license.org)

#### Introduction
This extension is inspired by [Codex](http://codex.caffeinated.ninja/codex). However, the implementation differs greatly.

#### Installation
- Configure a `base_route`. By default its is `doc` which makes DocIt accessable from `//your-domain.com/doc`. Leave empty if you want it to be the root url.  
- Configure a directory located somewhere in the public folder to be the root `projects_path`. As long as it's in the `public` directory, it doesn't matter how deep.
- Use the Artisan command to generate a new example project to start off with. You will notice the `{projects_path}/{project_slug}/project.php` configuration file and a directory `1.0`.
- The project will be available by going to `//your-domain/{base_route}/{project_slug}`, if you have more versions added, you can append a version number eg: `//your-domain/{base_route}/{project_slug}/2.3`




  
#### Features
- Documentation for your projects. Renders markdown files in a solid theme and provides several enhancements while keeping valid markdown.
- Version based documentation. Always allow users to view older documentation for any version they might use.
- 

### Copyright/License
Copyright 2015 [Robin Radic](https://github.com/RobinRadic) - [MIT Licensed](http://radic.mit-license.org)
