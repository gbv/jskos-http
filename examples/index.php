<?php

require 'service.php';

$server = new JSKOS\Server($service);

$response = $server->queryService($_GET, $_SERVER['PATH_INFO'] ?? '');
JSKOS\Server::sendResponse($response);
