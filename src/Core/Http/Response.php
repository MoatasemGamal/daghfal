<?php

namespace Core\Http;

class Response
{
    public static function redirect(string $path, int $response_code = 301, float $delay = 0): void
    {
        http_response_code($response_code);
        if (!$delay) {
            header("Location: $path");
        } else {
            header("Refresh:$delay; url=$path");
        }
        exit();
    }

    public static function backward(float $delay = 0, int $response_code = 301): void
    {
        http_response_code($response_code);
        $path = ($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "/";
        if (!$delay) {
            header("Location: $path");
        } else {
            header("Refresh:$delay; url=$path");
        }
        exit();
    }
}
