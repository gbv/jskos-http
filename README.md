# jskos-http - JSKOS API implementation

[![Latest Version](https://img.shields.io/packagist/v/gbv/jskos-http.svg?style=flat-square)](https://packagist.org/packages/gbv/jskos-http)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/gbv/jskos-http.svg?style=flat-square)](https://travis-ci.org/gbv/jskos-http)
[![Coverage Status](https://img.shields.io/coveralls/gbv/jskos-http/master.svg?style=flat-square)](https://coveralls.io/r/gbv/jskos-http)
[![Quality Score](https://img.shields.io/scrutinizer/g/gbv/jskos-http.svg?style=flat-square)](https://scrutinizer-ci.com/g/gbv/jskos-http)
[![Total Downloads](https://img.shields.io/packagist/dt/gbv/jskos-http.svg?style=flat-square)](https://packagist.org/packages/gbv/jskos)


# Requirements

Requires PHP 7.0 or PHP 7.1, package [jskos](https://packagist.org/packages/gbv/jskos), and any package listed as [php-http/client-implementation](https://packagist.org/providers/php-http/client-implementation).

Bugs and feature request are [tracked on GitHub](https://github.com/gbv/jskos-http/issues).

# Installation

## With composer

Install a [php-http/client-implementation](https://packagist.org/providers/php-http/client-implementation) and the latest version of this package, e.g.:

~~~bash
composer require php-http/curl-client gbv/jskos-http
~~~

This will automatically create `composer.json` for your project (unless it already exists) and add jskos-http as dependency. Composer also generates `vendor/autoload.php` to get autoloading of all dependencies: 

# Usage and examples

See directory [examples](examples) for example scripts and [jskos-php-examples](https://github.com/gbv/jskos-php-examples) for an example application.

## Client

See class `Client` to query JSKOS API and get back `Result` objects.

~~~php
use JSKOS\Client;

$client = new Client('http://example.org/');
$result = $client->query(['uri'=>$uri]);

if (count($result)) {
  ...
}
~~~

An optional `Http\Client\HttpClient` can be passed as second argument. Use this for instance to log all HTTP requests:

~~~php
$handler = \GuzzleHttp\HandlerStack::create();
foreach(['{method} {uri}', '{code} - {res_body}'] as $format) {
	$handler->unshift(
		\GuzzleHttp\Middleware::log(
			$logger, // e.g. Monolog\Logger
			new \GuzzleHttp\MessageFormatter($format)
		)
	);
}

$httpClient = \Http\Adapter\Guzzle6\Client::createWithConfig([
    'handler' => $handler,
]);

$jskoClient = new Client('http://example.org/', $httpClient);
~~~

## Server

Class `Server` wraps a JSKOS `Service` with [PSR-7 HTTP message interfaces](http://www.php-fig.org/psr/psr-7/) so it can be used with your favorite PSR-7 framework. An example with [Slim](https://packagist.org/packages/slim/slim):

~~~php
$server = new JSKOS\Server($service);

$app = new Slim\App();

$app->get('/api', function ($request) use ($server) {
    return $server->query($request);
});

$app->run();
~~~

But there is no need to use an additional framework to support simple HTTP GET requests:

~~~
use JSKOS;

$server = new Server($service);
$response = $server->queryService($_GET, $_SERVER['PATH_INFO'] ?? '');
Server::sendResponse($response);
~~~

# Author and License

Jakob Voß <jakob.voss@gbv.de>

JSKOS-HTTP is licensed under the LGPL license - see `LICENSE.md` for details.

# See also

JSKOS is created as part of project coli-conc: <https://coli-conc.gbv.de/>.

The current specification of JSKOS is available at <http://gbv.github.io/jskos/>.
