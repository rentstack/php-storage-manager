<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Exceptions\HttpException;
use App\Http\Endpoints;

try {
    Endpoints::routes([
        [
            'method' => 'GET',
            'uri' => '/',
            'class' => 'App\Controllers\IndexController',
            'function' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/(:xyz)',
            'class' => 'App\Controllers\ApiController',
            'function' => 'get',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/(:xyz)/(:xyz)',
            'class' => 'App\Controllers\ApiController',
            'function' => 'delete',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/(:xyz)/add',
            'class' => 'App\Controllers\ApiController',
            'function' => 'create',
        ],
    ]);
} catch (HttpException $e) {
    echo 'Error: ' . $e->getMessage();
}