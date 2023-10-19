<?php

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
    function view(string $path, $params = []): string
    {
        $path = VIEWS_PATH . str_replace(".", "/", $path) . ".view.php";
        if (!file_exists($path))
            return throw new \Exception("view not fount!", 404);

        ob_start();
        extract($params);
        include $path;
        return ob_get_clean();
    }
}