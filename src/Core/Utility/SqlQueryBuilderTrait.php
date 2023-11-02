<?php

namespace Core\Utility;

trait SqlQueryBuilderTrait
{
    private string $statement = "";
    private array $bindings = [];
    public static $lastInsertedId = null;
    public ?bool $passed = null;
    public function select(...$columns): self
    {
        if (isset($columns[0]) && is_array($columns[0]))
            $columns = array_shift($columns);
        if (empty($columns) || $columns == ['*'])
            $this->statement = "SELECT * ";
        elseif (is_array($columns)) {
            $columns = $this->prepareColumns($columns);
            $columns = implode(', ', $columns);
            $this->statement = "SELECT " . $columns . " ";
        } else
            $this->statement = "SELECT " . $columns . " ";

        return $this;
    }
    public function subQuery(string $sql, string $alias = "")
    {
        if ($sql[0] === '(' && $sql[strlen($sql) - 1] === ')') {
            $sql = rtrim($sql, ")");
            $sql = ltrim($sql, "(");
        }

        if (str_contains(strtolower($sql), "limit"))
            $this->statement .= "( $sql ) $alias ";
        else
            $this->statement .= "( $sql LIMIT 1) $alias ";
        return $this;
    }
    public function from(string $table, string $alias = ""): self
    {
        $table = $this->prepareTable($table, $alias);
        $this->statement .= "FROM $table ";
        return $this;
    }

    public function insert(string $table, string $alias = ""): self
    {
        $table = $this->prepareTable($table, $alias);
        $this->statement = "INSERT INTO $table ";
        return $this;
    }
    public function update(string $table, string $alias = ""): self
    {
        $table = $this->prepareTable($table, $alias);
        $this->statement = "UPDATE $table ";
        return $this;
    }
    public function data(array $data)
    {
        $this->addBindings($data);
        $this->statement .= "SET " . $this->fullyImplode($data, "=", ",") . " ";
        return $this;
    }
    public function delete(string $table, string $alias = ""): self
    {
        $table = $this->prepareTable($table, $alias);
        $this->statement = "DELETE FROM $table ";
        return $this;
    }
    public function leftJoin(string $table, string $alias = ""): self
    {
        $table = $this->prepareTable($table, $alias);
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
    public function append($sql)
    {
        $this->statement .= $sql . " ";
        return $this;
    }
    //==================================
    //private functions section
    //==================================
    private function logical($logical, array $cols, $operator = "=", $boolean = "and"): self
    {
        $columns = $this->fullyImplode($cols, $operator, $boolean);
        $this->statement .= "$logical $columns ";
        $this->addBindings($cols);
        return $this;
    }
    private function addBindings(array $bindings): void
    {
        $this->bindings = array_merge($this->bindings, array_values($bindings));
    }
    private function prepareTable($table, $alias = ""): string
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
    private function fullyImplode(array $columns, $operator, $booleanOrSeparator): string
    {
        $operator = strtoupper($operator);
        $boolean = strtoupper($booleanOrSeparator);

        $keys = $this->prepareColumns($columns);

        $columns = [];
        foreach ($keys as $key) {
            $columns[] = "$key $operator ?";
        }
        return implode(" $boolean ", $columns);
    }
    private function prepareCol($col): string
    {
        $col = explode('.', $col);
        $col[count($col) - 1] = trim(end($col), "`");
        $col[count($col) - 1] = "`" . end($col) . "`";
        return implode('.', $col);
    }
    private function reset()
    {
        $this->statement = "";
        $this->bindings = [];
        $this->passed = null;
    }
}

