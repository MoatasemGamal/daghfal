<?php

namespace Core\Utility;

trait SqlQueryBuilderTrait
{
    public string $statement = "";
    public array $bindings = [];

    public function select(...$columns): self
    {
        if (!empty($columns)) {
            $columns = $this->prepareColumns($columns);
            $columns = implode(', ', $columns);
            $this->statement = "SELECT " . $columns . " ";
        } else
            $this->statement = "SELECT * ";
        return $this;
    }
    public function from(string $table, string $alias = ""): self
    {
        $table = $this->setTable($table, $alias);
        $this->statement .= "FROM $table ";
        return $this;
    }

    public function insertInto(string $table, string $alias = ""): self
    {
        $table = $this->setTable($table, $alias);
        $this->statement = "INSERT INTO $table ";
        return $this;
    }


    public function leftJoin(string $table, string $alias = ""): self
    {
        $table = $this->setTable($table, $alias);
        $this->statement .= "LEFT JOIN $table ";
        return $this;
    }
    public function on(string $on): self
    {
        $this->statement .= "ON $on ";
        return $this;
    }

    public function where(array $cols, string $operator = "=", string $boolean = "and"): self
    {

        $columns = $this->fullyImplode($cols, $operator, $boolean);
        $this->statement .= "WHERE $columns ";
        $this->addBindings($cols);
        return $this;
    }
    public function or (array $cols, string $operator = "=", string $boolean = "and"): self
    {
        return $this->logical("OR", $cols, $operator, $boolean);
    }
    public function and (array $cols, string $operator = "=", string $boolean = "and"): self
    {
        return $this->logical("AND", $cols, $operator, $boolean);
    }

    public function groupBy(string $column): self
    {
        $column = $this->prepareCol($column);
        $this->statement .= "GROUP BY $column ";
        return $this;
    }
    public function orderBy(string $column, string $order = "desc"): self
    {
        $column = $this->prepareCol($column);
        $order = match (strtoupper($order)) {
            "ASC" => "ASC",
            "ASCENDING" => "ASC",
            "DESCENDING" => "DESC",
            default => "DESC"
        };
        $this->statement .= "ORDER BY $column $order ";
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->statement .= "LIMIT $limit ";
        return $this;
    }
    public function offset(int $offset): self
    {
        $this->statement .= "OFFSET $offset ";
        return $this;
    }









    private function logical($logical, array $cols, $operator = "=", $boolean = "and")
    {
        $columns = $this->fullyImplode($cols, $operator, $boolean);
        $this->statement .= "$logical $columns ";
        $this->addBindings($cols);
        return $this;
    }
    private function addBindings(array $bindings)
    {
        $bindings = $this->prepareBindings($bindings);
        $this->bindings = array_merge($this->bindings, $bindings);
    }
    private function setTable($table, $alias = "")
    {
        return ("`$table`" . $alias);
    }

    private function prepareColumns(array $columns): array
    {
        if (!array_is_list($columns))
            $columns = array_keys($columns);
        $columns = array_map(fn($column) => $this->prepareCol($column), $columns);
        return $columns;
    }
    private function prepareBindings(array $columns)
    {
        $bindings = [];
        if (array_is_list($columns))
            return $columns;
        foreach ($columns as $key => $value) {
            $key = explode(".", $key);
            $key = end($key);
            $key = ":$key";
            $bindings[$key] = $value;
        }
        return $bindings;
    }
    private function fullyImplode(array $columns, $operator, $boolean)
    {
        $operator = strtoupper($operator);
        $boolean = strtoupper($boolean);

        $keys = $this->prepareColumns($columns);

        $values = array_map(function ($col) {
            $col = explode(".", $col);
            $col = end($col);
            return ":$col";
        }, array_keys($columns));

        $columns = [];
        foreach (array_combine($keys, $values) as $key => $value) {
            $columns[] = "$key $operator $value";
        }
        return implode(" $boolean ", $columns);
    }

    private function prepareCol($col)
    {
        $col = explode('.', $col);
        $col[count($col) - 1] = trim(end($col), "`");
        $col[count($col) - 1] = "`" . end($col) . "`";
        return implode('.', $col);
    }
}

