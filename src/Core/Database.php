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
        } elseif (is_array($bindings) && !empty($bindings))
            $bindings = $this->prepareBindings($bindings);
        $stmt = $this->pdo->prepare($query);
        foreach ((array) $bindings as $key => $value) {
            $key = is_int($key) ? $key + 1 : $key;
            if (is_array($value))
                $stmt->bindParam($key, $value[0], $value[1]);
            else
                $stmt->bindParam($key, $value);
        }
        $stmt->execute();
        return $stmt;
    }

}