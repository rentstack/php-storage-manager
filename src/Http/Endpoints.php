<?php

namespace App\Http;
use App\Exceptions\HttpException;
use const JSON_PRETTY_PRINT;

class Endpoints
{
    /**
     * @param array $list
     * @return void
     * @throws HttpException
     */
    public static function routes(array $list): void
    {
        foreach ($list as $route) {
            $method = $route['method'];
            Router::$method($route['uri'], $route['class'].'@'.$route['function']);
        }
        Router::dispatch();
    }

    /**
     * @param array $response
     * @return void
     */
    public static function json(array $response): void
    {
        $http_codes = [
            '200' => '200 OK',
            '404' => '404 Not Found',
            '500' => '500 Internal Server Error'
        ];
        header($_SERVER['SERVER_PROTOCOL'].' '.$http_codes[$response['code']]);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }
}