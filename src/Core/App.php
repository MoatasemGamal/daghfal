<?php

namespace Core;

use Core\Bases\BaseController;
use Core\Utility\Session;

class App
{
    public static ?App $singleton = null;
    private array $configurations;
    public BaseController $controller;
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

    private function loadRoutes(): void
    {
        $routesFiles = glob(ROUTES_PATH . "*.php");
        foreach ((array) $routesFiles as $routeFile) {
            require_once $routeFile;
        }
    }
    public static function isGuest(): bool
    {
        Session::start();
        return !isset($_SESSION["user"]);
    }
    public function run(): void
    {
        $this->loadRoutes();
        try {
            echo Route::resolve();
        } catch (\Exception $e) {
            echo view('_error', ['error' => $e]);
        }
    }
}
