<?php

require __DIR__.'/../vendor/autoload.php';

$service = new JSKOS\CallableService(function ($query, $path) {
    return new JSKOS\Result([
        new JSKOS\Concept([
            'scopeNote' => [
                'en' => [
                    'dummy concept echoing query parameters and path'
                ]
            ],
            'prefLabel' => [
                'und' => $path
            ],
            'altLabel' => [
                'und' => array_map(
                    function ($key, $value) {
                        return "$key=$value";
                    },
                    array_keys($query), $query
                )
            ]
        ])
    ]);
});
