<?php
declare(strict_types=1);
use Core\App;
use Core\Route;


require_once(dirname(__DIR__) . "/src/config.php");
require_once(SRC_PATH . "vendor/autoload.php");
require_once(CORE_PATH . "helpers.php");

Route::get("/", "index");

$configurations = [];
App::init($configurations);

App::$singleton->run();