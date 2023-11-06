<?php

namespace Core\Bases;

use Core\Database;

/**
 * @property bool $timestamps
 * @property string $UPDATED_AT
 * @property string $CREATED_AT
 * @property string $DELETED_AT
 */
class BaseModel
{
    protected static string $table;
    protected static array $attributes;
    protected static string $primaryKey = 'id';

    public static function create(array $attributes = []): BaseModel
    {
        $model = new static();
        foreach ($attributes as $key => $value) {
            $model->$key = $value;
        }
        return $model;
    }
    public function edit(array $attributes = []): BaseModel
    {
        foreach ($attributes as $key => $value) {
            if (isset($this->$key))
                $this->$key = $value;
        }
        return $this;
    }

    public static function all(...$columns)
    {
        if (isset($columns[0]) && is_array($columns[0]))
            $columns = $columns[0];
        app('db')->select($columns)->from(static::tableName());
        if (isset(static::$DELETED_AT))
            app('db')->isNull(static::$DELETED_AT);
        $objs = app('db')->run()->fetchAll(\PDO::FETCH_CLASS, static::class);
        return $objs;
    }
    public static function allWithTrashed(...$columns)
    {
        if (isset($columns[0]) && is_array($columns[0]))
            $columns = $columns[0];
        $objs = app('db')->select($columns)->from(static::tableName())->run()->fetchAll(\PDO::FETCH_CLASS, static::class);
        return $objs;
    }
    public static function oneOrFail(array $whereCols, string $operator = "=", string $boolean = "and")
    {
        app('db')->select()->from(static::tableName())
            ->where($whereCols, $operator, $boolean);
        if (isset(static::$DELETED_AT))
            app('db')->isNull(static::$DELETED_AT);
        $obj = app('db')->run()->fetchObject(static::class);
        if ($obj)
            return $obj;
        else
            throw new \Exception('Not found', 404);
    }
    public static function oneOrFailWithTrashed(array $whereCols, string $operator = "=", string $boolean = "and")
    {
        $obj = app('db')->select()->from(static::tableName())
            ->where($whereCols, $operator, $boolean)
            ->run()->fetchObject(static::class);
        if ($obj)
            return $obj;
        else
            throw new \Exception('Not found', 404);
    }
    public static function one(array $whereCols, string $operator = "=", string $boolean = "and")
    {
        app('db')->select()->from(static::tableName())
            ->where($whereCols, $operator, $boolean);
        if (isset(static::$DELETED_AT))
            app('db')->isNull(static::$DELETED_AT);
        $obj = app('db')->run()->fetchObject(static::class);
        return $obj;
    }
    public static function oneWithTrashed(array $whereCols, string $operator = "=", string $boolean = "and")
    {
        $obj = app('db')->select()->from(static::tableName())
            ->where($whereCols, $operator, $boolean)
            ->run()->fetchObject(static::class);
        return $obj;
    }
    //get specific one or more
    public static function where(array $cols = [], string $operator = "=", string $boolean = "and"): Database
    {
        app('db')->fetchClass = static::class;
        if (empty(app('db')->statement))
            app('db')->select()->from(static::tableName());
        return app('db')->where($cols, $operator, $boolean);
    }

    public function delete()
    {
        if (isset(static::$DELETED_AT)) {
            $this->{static::$DELETED_AT} = date("Y-m-d H:i:s", time());
            $this->save();
        } else {
            $this->forceDelete();
        }
    }

    public function forceDelete(): void
    {
        app('db')->delete(static::tableName())->where(["`" . static::$primaryKey . "`" => $this->{static::$primaryKey}])->run();
    }

    public function save()
    {
        if (isset($this->{static::$primaryKey}) && !is_null($this->{static::$primaryKey})) {
            $this->update();
        } else {
            $this->insert();
        }
        return $this;
    }

    private function update()
    {
        if (static::$timestamps === true) {
            $this->{static::$UPDATED_AT} = date("Y-m-d H:i:s", time());
        }
        app('db')->update(static::tableName())
            ->data($this->getData())
            ->where([static::$primaryKey => $this->{static::$primaryKey}])
            ->run();
    }

    private function insert()
    {
        if (isset(static::$timestamps) && static::$timestamps === true) {
            $d = date("Y-m-d H:i:s", time());
            $this->{static::$CREATED_AT} = $d;
            $this->{static::$UPDATED_AT} = $d;
        }
        app('db')->insert(static::tableName())
            ->data($this->getData())
            ->run();
        $this->{static::$primaryKey} = app('db')::$lastInsertedId;
    }

    private function getData()
    {
        $data = [];
        foreach (static::$attributes as $attr) {
            if (isset($this->$attr))
                $data[$attr] = $this->$attr;
        }
        if (isset(static::$timestamps) && static::$timestamps === true) {
            $data[static::$CREATED_AT] = $this->{static::$CREATED_AT};
            $data[static::$UPDATED_AT] = $this->{static::$UPDATED_AT};
        }
        if (isset(static::$DELETED_AT))
            $data[static::$DELETED_AT] = $this->{static::$DELETED_AT};
        return $data;
    }
    public static function tableName()
    {
        if (!isset(static::$table) || is_null(static::$table)) {
            $table = explode('\\', static::class);
            $table = end($table);
            $table = strtolower($table);
            return $table[strlen($table) - 1] === 's' ? $table : $table . "s";
        }
        return static::$table;
    }
    // ===================================
    // ============ Relations ============
    // ===================================
    // - one to one relationship
    public function hasOne(string $otherClass, $forigenKey = null, $primaryKey = null, bool $withTrashed = false)
    {
        if ($forigenKey == null)
            $forigenKey = $otherClass::$primaryKey;
        if ($primaryKey == null)
            $primaryKey = static::class::$primaryKey;
        $pkValue = $this->{$primaryKey};
        if ($withTrashed)
            return $otherClass::oneWithTrashed([$forigenKey => $pkValue]);
        else
            return $otherClass::one([$forigenKey => $pkValue]);
    }
    public function hasMany(string $otherClass, $forigenKey = null, $primaryKey = null, bool $withTrashed = false)
    {
        if ($forigenKey == null)
            $forigenKey = $otherClass::$primaryKey;
        if ($primaryKey == null)
            $primaryKey = static::class::$primaryKey;
        $pkValue = $this->{$primaryKey};
        if ($withTrashed)
            return $otherClass::allWithTrashed([$forigenKey => $pkValue]);
        else
            return $otherClass::all([$forigenKey => $pkValue]);
    }


    public function __get($name)
    {
        $property = $name;
        $name = explode('_', $name);
        foreach ($name as &$n) {
            $n = ucfirst($n);
        }
        $name = implode('', $name);
        $getMethod = "get" . $name . "Attribute";
        if (isset($this->{$property}) && method_exists(static::class, $getMethod)) {
            $method = new \ReflectionMethod(static::class, $getMethod);
            if ($method->getNumberOfParameters() > 0)
                return $this->$getMethod($this->$property);
            else
                return $this->$getMethod();
        } elseif (isset($this->{$property}))
            return $this->{$property};
        else
            return null;
    }
    public function __set($name, $value)
    {
        $property = $name;
        $name = explode('_', $name);
        foreach ($name as &$n) {
            $n = ucfirst($n);
        }
        $name = implode('', $name);
        $setMethod = "set" . $name . "Attribute";
        if (method_exists(static::class, $setMethod))
            $this->$setMethod($value);
        else
            $this->$name = $value;
    }
}