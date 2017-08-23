<?php

if (php_sapi_name() != "cli") exit;
if (count($argv) < 2) {
    print "usage: php {$argv[0]} baseURL [key=value ...]\n";
    exit;
}

require __DIR__ . '/../vendor/autoload.php';

$query = [];
$path = '';
foreach (array_slice($argv,2) as $param) {
    if (preg_match('/([a-z]+)=(.*)/', $param, $match)) {
        $query[$match[1]] = $match[2];
    } else {
        $path = $param;
    }
}

$client = new JSKOS\Client($argv[1]);

$jskos = $client->query($query, $path);
print $jskos->json() . "\n";
