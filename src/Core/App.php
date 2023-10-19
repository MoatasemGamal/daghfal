<?php

namespace Core;


class App
{
    public static ?App $singleton = null;
    private array $configurations;
    private function __construct($configurations)
    {
        $this->configurations = $configurations;
    }
    /**
     * Initialization singleton of App
     * @param array $configurations
     * @return \Core\App
     */
    public static function init(array $configurations = null): self
    {
        if (is_null(static::$singleton))
            static::$singleton = new App($configurations);
        return static::$singleton;
    }

    public function run()
    {
        try {
            echo Route::resolve();
        } catch (\Exception $e) {
            echo view('_error', ['error' => $e]);
        }
    }
}
