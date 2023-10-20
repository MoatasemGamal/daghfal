<?php
declare(strict_types=1);
use Core\App;


require_once(dirname(__DIR__) . "/src/config.php");
require_once(SRC_PATH . "vendor/autoload.php");
require_once(CORE_PATH . "helpers.php");

session_name("SESSID");
session_save_path(SESSIONS_PATH);

$configurations = [
    "database" => [
        "dsn" => DSN,
        "username" => DB_USERNAME,
        "password" => DB_PASSWORD,
        "pdo_options" => PDO_OPTIONS
    ]
];

App::init($configurations);

App::$singleton->run();