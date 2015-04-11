<!---
title: Overview
author: Robin Radic
icon: fa fa-eye
toc:
    introduction: Introduction
    examples: Some examples
    copyright: Copyright   
-->

<!---
[![Build Status](https://img.shields.io/travis/RobinRadic/blade-extensions.svg?branch=master&style=flat-square)](https://travis-ci.org/RobinRadic/blade-extensions)
[![GitHub Version](https://img.shields.io/github/tag/robinradic/blade-extensions.svg?style=flat-square&label=version)](http://badge.fury.io/gh/robinradic%2Fblade-extensions)
[![Code Coverage](https://img.shields.io/badge/coverage-100%-green.svg?style=flat-square)](http://robin.radic.nl/blade-extensions/coverage)
[![Total Downloads](https://img.shields.io/packagist/dt/radic/blade-extensions.svg?style=flat-square)](https://packagist.org/packages/radic/blade-extensions)
[![License](http://img.shields.io/badge/license-MIT-ff69b4.svg?style=flat-square)](http://radic.mit-license.org)

[![Goto Documentation](http://img.shields.io/badge/goto-docs-orange.svg?style=flat-square)](http://docs.radic.nl/blade-extensions)
[![Goto API Documentation](https://img.shields.io/badge/goto-api--docs-orange.svg?style=flat-square)](http://radic.nl:8080/job/blade-extensions/PHPDOX_Documentation/)
[![Goto Repository](http://img.shields.io/badge/goto-repo-orange.svg?style=flat-square)](https://github.com/robinradic/blade-extensions)
-->

### Overview
**Laravel 5** package providing a feature rich, extendable and easily integrated solution for generating and maintaining documentation for your software.
Using markdown files, it introduces **MarkDoc** tags that enhances the output while maintaining completely valid markdown documents.
It is possible to simply put the markdown files into a directory and DocIt will generate the pages.
It's even possible to attach it to github, automaticly pulling in the markdown files from your repo branches and tags whenever you push a commit.

But it doesn't stop there. If wanted, you could enable the github login for your project which gives users with access to the repository the
option to alter pages on the website and committing it to github (after which github webhook will be notified, and DocIt will update aswell!)
  
  
### For all languages
Even though DocIt is created with PHP, there's no reason to only use it for PHP documentation. It's usefull for any kind of software package
and therefor perfect if you'd like to use it for projects using other languages.


### Copyright/License
Copyright 2015 [Robin Radic](https://github.com/RobinRadic) - [MIT Licensed](http://radic.mit-license.org)
