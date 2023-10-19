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