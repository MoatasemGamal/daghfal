<?php
use Core\App;
use Core\Database;
use Core\Utility\Session;
use Core\Utility\View;

if (!function_exists("pre")) {
    /**
     * var_dump or print_r with <pre></pre>
     * @param mixed $variable
     * @param bool $print_r
     * @return void
     */
    function pre(mixed $variable, bool $print_r = false): void
    {
        echo "<pre dir='ltr'>";
        if ($print_r)
            print_r($variable);
        else
            var_dump($variable);
        echo "</pre>";
    }
}

if (!function_exists("view")) {
    function view(string $path, $data = []): string
    {
        $view = new View($path, $data);
        return $view->render();
    }
}

if (!function_exists("app")) {

    /**
     * get App::$singleton or any of its attributes like App::$singleton->db
     * @param string $attr
     * @return mixed
     */
    function app(string $attr = 'self'): mixed
    {
        if ($attr === 'self')
            return App::$singleton;
        elseif (isset(App::$singleton->$attr))
            return App::$singleton->$attr;
        else
            return null;
    }

}