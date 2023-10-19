<?php

namespace Core;

use Core\Http\Request;

class Route
{
    private static array $routes = [];
    public static function post(string $path, array|string|callable $action): void
    {
        static::replaceWithPattern($path);
        static::$routes['post'][$path] = $action;
    }
    public static function get(string $path, array|string|callable $action): void
    {
        static::replaceWithPattern($path);
        static::$routes['get'][$path] = $action;
    }

    public static function resolve()
    {
        $method = Request::method();
        $path = (Request::path() !== '/') ? rtrim(Request::path(), '/') : Request::path();
        $action = false;
        $params = [];
        $routesPaths = array_keys(static::$routes[$method] ?? []);
        foreach ($routesPaths as $routePath) {
            if (preg_match_all($routePath, $path, $matches)) {
                $action = static::$routes[$method][$routePath];
                foreach ($matches as $group) {
                    foreach ($group as $item) {
                        $params[] = $item;
                    }
                }
                array_shift($params);
                break;
            }
        }
        if (!$action) {
            return throw new \Exception("This page not found", 404);
        }
        if (is_string($action)) {
            return view($action);
        }

        return call_user_func($action);
    }

    private static function replaceWithPattern(&$path)
    {
        $pattern = "#\{\{[^'/]*\}\}#";
        if (preg_match_all($pattern, $path, $matches)) {
            $pattern = "([^'/]*)";
            foreach ($matches[0] as $match) {
                $path = str_replace($match, $pattern, $path);
            }
        }
        $path = '#^' . $path . '$#';
    }
}
