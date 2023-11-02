<?php

namespace Core;

use Core\Utility\SqlQueryBuilderTrait;

class Database
{
    public static ?Database $singleton = null;
    public ?\PDO $pdo = null;
    use SqlQueryBuilderTrait;
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

    public function run($query = "CURRENT_QUERY", $bindings = null): \PDOStatement
    {
        if ($query == "CURRENT_QUERY") {
            $query = $this->statement;
            $bindings = $this->bindings;
        }
        $stmt = $this->pdo->prepare($query);
        foreach ((array) $bindings as $key => $value) {
            $key = is_int($key) ? $key + 1 : $key;
            if (is_array($value))
                $stmt->bindValue($key, $value[0], $value[1]);
            else
                $stmt->bindValue($key, $value);
        }
        $this->passed = (bool) $stmt->execute();
        if ($this->passed)
            self::$lastInsertedId = $this->pdo->lastInsertId();
        $this->reset();
        return $stmt;
    }

}