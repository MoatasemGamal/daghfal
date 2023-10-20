<?php

namespace Core;

class Database
{
    public static ?Database $singleton = null;
    public ?\PDO $pdo = null;
    private function __construct(array $config)
    {
        try {

            $this->pdo = new \PDO($config["dsn"], $config["username"], $config["password"], $config["pdo_options"]);
        } catch (\Throwable $th) {
            die(view("_error", ["error" => $th]));
        }
    }

    public static function init(array $configurations = null): self
    {
        if (is_null(self::$singleton))
            self::$singleton = new Database($configurations);
        return self::$singleton;
    }

}