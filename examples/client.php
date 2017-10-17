<?php

if (php_sapi_name() != "cli") exit;
$self = array_shift($argv);
if (count($argv) < 1) {
    print "usage: php $self [-v] baseURL [key=value ...]\n";
    exit;
}

require __DIR__ . '/../vendor/autoload.php';

$config = [];

if ($argv[0] == '-v') {
	array_shift($argv);
	$logger = new class extends \Psr\Log\AbstractLogger {
		public function log($level, $message, array $context = [])
		{ 
			fwrite(STDERR, "$level $message\n");
		}
	};

	$config['handler'] = \GuzzleHttp\HandlerStack::create();
	foreach(['{method} {uri}', '{code} - {res_body}'] as $format) {
		$config['handler']->unshift(
			\GuzzleHttp\Middleware::log(
				$logger, // e.g. Monolog\Logger
				new \GuzzleHttp\MessageFormatter($format)
			)
		);
	}
}

$baseURL = array_shift($argv);

$query = [];
$path = '';
foreach ($argv as $param) {
    if (preg_match('/([a-z]+)=(.*)/', $param, $match)) {
        $query[$match[1]] = $match[2];
    } else {
        $path = $param;
    }
}

$httpClient = \Http\Adapter\Guzzle6\Client::createWithConfig($config);
$jskosClient = new JSKOS\Client($baseURL, $httpClient);

$jskos = $jskosClient->query($query, $path);
print $jskos->json() . "\n";
