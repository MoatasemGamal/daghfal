<?php
declare(strict_types=1);
use Core\App;


require_once(dirname(__DIR__) . "/src/config.php");
require_once(SRC_PATH . "vendor/autoload.php");
require_once(CORE_PATH . "helpers.php");


$configurations = [];
App::init($configurations);

App::$singleton->run();