# jskos-http - JSKOS API implementation

[![Latest Version](https://img.shields.io/packagist/v/gbv/jskos-http.svg?style=flat-square)](https://packagist.org/packages/gbv/jskos)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/gbv/jskos-http.svg?style=flat-square)](https://travis-ci.org/gbv/jskos-http)
[![Coverage Status](https://img.shields.io/coveralls/gbv/jskos-http/master.svg?style=flat-square)](https://coveralls.io/r/gbv/jskos-http)
[![Quality Score](https://img.shields.io/scrutinizer/g/gbv/jskos-http.svg?style=flat-square)](https://scrutinizer-ci.com/g/gbv/jskos-http)
[![Total Downloads](https://img.shields.io/packagist/dt/gbv/jskos-http.svg?style=flat-square)](https://packagist.org/packages/gbv/jskos)


# Requirements

Requires PHP 7.0 or PHP 7.1 and package [jskos](https://packagist.org/packages/gbv/jskos).

Bugs and feature request are [tracked on GitHub](https://github.com/gbv/jskos-http/issues).

# Installation

## With composer

Install the latest version with

~~~bash
composer require gbv/jskos-http
~~~

This will automatically create `composer.json` for your project (unless it already exists) and add jskos-http as dependency. Composer also generates `vendor/autoload.php` to get autoloading of all dependencies: 
# Usage and examples

The [jskos-php-examples repository](https://github.com/gbv/jskos-php-examples)
contains several examples, including wrappers of existing terminology services
(Wikidata, GND...) to JSKOS-API.

The examples can be tried online at <https://jskos-php-examples.herokuapp.com>.

# Author and License

Jakob Voß <jakob.voss@gbv.de>

JSKOS-HTTP is licensed under the LGPL license - see `LICENSE.md` for details.

# See alse

JSKOS is created as part of project coli-conc: <https://coli-conc.gbv.de/>.

The current specification of JSKOS is available at <http://gbv.github.io/jskos/>.
