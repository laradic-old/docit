<!---
title: Overview
author: Robin Radic
icon: fa fa-eye
toc:
    introduction: Introduction
    examples: Some examples
    copyright: Copyright   
-->


[![Build Status](https://img.shields.io/travis/RobinRadic/blade-extensions.svg?branch=master&style=flat-square)](https://travis-ci.org/RobinRadic/blade-extensions)
[![GitHub Version](https://img.shields.io/github/tag/robinradic/blade-extensions.svg?style=flat-square&label=version)](http://badge.fury.io/gh/robinradic%2Fblade-extensions)
[![Code Coverage](https://img.shields.io/badge/coverage-100%-green.svg?style=flat-square)](http://robin.radic.nl/blade-extensions/coverage)
[![Total Downloads](https://img.shields.io/packagist/dt/radic/blade-extensions.svg?style=flat-square)](https://packagist.org/packages/radic/blade-extensions)
[![License](http://img.shields.io/badge/license-MIT-ff69b4.svg?style=flat-square)](http://radic.mit-license.org)
[![Goto Documentation](http://img.shields.io/badge/goto-docs-orange.svg?style=flat-square)](http://robinradic.github.io/blade-extensions)
[![Goto API Documentation](https://img.shields.io/badge/goto-api--docs-orange.svg?style=flat-square)](http://robin.radic.nl/blade-extensions/api)
[![Goto Repository](http://img.shields.io/badge/goto-repo-orange.svg?style=flat-square)](https://github.com/robinradic/blade-extensions)

**Laravel 5** package providing additional Blade functionality.

<!---+ row +-->
<!---+ col-md-6 +-->
- **@set @unset** Setting and unsetting of values
- **@foreach @break @continue** Loop data and extras
- **@partial @block @render** Creating view partials and blocks. Nest them, extend them, render them.
<!---+ /col-md-6 +-->
<!---+ col-md-6 +-->
- **@macro** Defining and running macros
- **@debug** Debugging values in views
- **BladeViewTestingTrait** enables all assert methods from your test class in your view as directives. `@assertTrue($hasIt)..`
<!---+ /col-md-6 +-->
<!---+ /row +-->

<a name="examples"></a>
#### Some examples
```php
@set('newvar', 'value')
{{ $newvar }}

@debug($somearr)
```


<a name="copyright"></a>
#### Copyright/License
Copyright 2015 [Robin Radic](https://github.com/RobinRadic) - [MIT Licensed](http://radic.mit-license.org)
