<?php

namespace Core\Http;

class Request
{
    /**
     * Get Current Request Path without GET parameters
     * @return string
     */
    public static function path(): string
    {
        $path = $_SERVER["REQUEST_URI"];
        return (strpos($path, '?') === false) ? $path : substr($path, 0, strpos($path, '?'));
    }

    /**
     * Get Current Request Path Method in lowercase (get, post)
     * @return string
     */
    public static function method(): string
    {
        return strtolower($_SERVER["REQUEST_METHOD"]);
    }

    public static function isGet(): bool
    {
        return static::method() == 'get';
    }

    public static function isPost(): bool
    {
        return static::method() == 'post';
    }
}
