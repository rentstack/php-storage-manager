<?php

namespace App\Http;
use App\Exceptions\HttpException;

/**
 * @method static get(string $uri, string $controller)
 * @method static post(string $uri, string $controller)
 * @method static delete(string $uri, string $controller)
 */

class Router
{

    /**
     * @var array
     */
    protected static array $routes = [];

    /**
     * @var array
     */
    protected static array $methods = [];

    /**
     * @var array
     */
    protected static array $callbacks = [];

    /**
     * @var array
     */
    protected static array $patterns = [
        ':abc' => '[a-zA-Z]+',
        ':xyz' => '[a-zA-Z0-9]+',
        ':any' => '[^/]+',
        ':num' => '[0-9]+',
        ':all' => '.*'
    ];

    /**
     * @param string $method
     * @param array $params
     * @return void
     */
    public static function __callstatic(string $method, array $params): void
    {
        $callback = $params[1];
        $uri = dirname($_SERVER['PHP_SELF']).'/'.$params[0];
        array_push(self::$routes, $uri);
        array_push(self::$methods, strtoupper($method));
        array_push(self::$callbacks, $callback);
    }

    /**
     * Dispatch
     * @throws HttpException
     * @return void
     */
    public static function dispatch(): void
    {
        $exist_route = false;
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $replaces = array_values(static::$patterns);
        $searches = array_keys(static::$patterns);
        foreach (self::$routes as $key => $value) {
            self::$routes[$key] =
                preg_replace('/\/+/', '/', $value);
        }
        if (in_array($uri, self::$routes)) {
            $route_array = array_keys(self::$routes, $uri);
            foreach ($route_array as $route) {
                if (self::$methods[$route] == $method || self::$methods[$route] == 'ANY') {
                    $exist_route = true;
                    if (!is_object(self::$callbacks[$route])) {
                        $parts = explode('/', self::$callbacks[$route]);
                        $last = end($parts);
                        $segment = explode('@',$last);
                        $controller = new $segment[0]();
                        $controller->{$segment[1]}();
                    } else {
                        call_user_func(self::$callbacks[$route]);
                    }
                }
            }
        } else {
            $i = 0;
            $route_array = array_values(self::$routes);
            foreach ($route_array as $route) {
                if (str_contains($route, ':')) {
                    $route = str_replace($searches, $replaces, $route);
                }
                if (preg_match('#^' . $route . '$#', $uri, $matched)) {
                    if (self::$methods[$i] == $method || self::$methods[$i] == 'ANY') {
                        $exist_route = true;
                        array_shift($matched);
                        if (!is_object(self::$callbacks[$i])) {
                            $parts = explode('/', self::$callbacks[$i]);
                            $last = end($parts);
                            $segment = explode('@', $last);
                            if (!class_exists($segment[0])) {
                                throw new HttpException('Controller class "' . $segment[0] . '" is not found.');
                            }
                            $controller = new $segment[0]();
                            if (!method_exists($controller, $segment[1])) {
                                throw new HttpException('Method "' . $segment[1] . '" is not implemented in class ' . $segment[0]);
                            } else {
                                call_user_func_array(array($controller, $segment[1]), $matched);
                            }
                        } else {
                            call_user_func_array(self::$callbacks[$i], $matched);
                        }
                    }
                }
                $i++;
            }
        }
        if ($exist_route == false) {
            call_user_func(function() {
                Endpoints::json([
                    'status' => false,
                    'code' => 404,
                    'data' => ['message' => 'This endpoint is not implemented.'],
                ]);
            });
        }
    }
}