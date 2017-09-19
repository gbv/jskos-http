<?php

require 'vendor/autoload.php';
require '../service.php';

$server = new JSKOS\Server($service);
    
$app = new Slim\App();

$app->get('/{path:.*}', function ($request) use ($server) {
    return $server->query($request);
});

$app->run();
