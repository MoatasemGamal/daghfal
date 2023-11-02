<?php

namespace Core\Bases;

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

    public static function factory(array $attributes = []): BaseModel
    {
        $model = new static();
        foreach ($attributes as $key => $value) {
            $model->$key = $value;
        }
        return $model;
    }
    public function edit(array $attributes = []): void
    {
        foreach ($attributes as $key => $value) {
            if (isset($this->$key))
                $this->$key = $value;
        }
    }

    public static function all(...$columns)
    {
        if (isset($columns[0]) && is_array($columns[0]))
            $columns = $columns[0];
        $where = isset(static::$DELETED_AT) ? "`" . static::$DELETED_AT . "` IS NULL" : "";
        $objs = app('db')->select($columns)->from(static::$table)->where($where)->run()->fetchAll(\PDO::FETCH_CLASS, static::class);
        return $objs;
    }
    public static function allWithTrashed(...$columns)
    {
        if (isset($columns[0]) && is_array($columns[0]))
            $columns = $columns[0];
        $objs = app('db')->select($columns)->from(static::$table)->run()->fetchAll(\PDO::FETCH_CLASS, static::class);
        return $objs;
    }
    public static function oneOrFail()
    {
        $where = isset(static::$DELETED_AT) ? "`" . static::$DELETED_AT . "` IS NULL" : "";
        $obj = app('db')->select()->from(static::$table)->where($where)->run()->fetchObject(static::class);
        if ($obj)
            return $obj;
        else
            throw new \Exception('Not found', 404);
    }
    public static function oneOrFailWithTrashed()
    {
        $obj = app('db')->select()->from(static::$table)->where()->run()->fetchObject(static::class);
        if ($obj)
            return $obj;
        else
            throw new \Exception('Not found', 404);
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
        app('db')->delete(static::$table)->where(["`" . static::$primaryKey . "`" => $this->{static::$primaryKey}])->run();
    }

    public function save()
    {
        if (isset($this->{static::$primaryKey}) && !is_null($this->{static::$primaryKey})) {
            $this->update();
        } else {
            $this->insert();
        }
    }

    private function update()
    {
        if (static::$timestamps === true) {
            $this->{static::$UPDATED_AT} = date("Y-m-d H:i:s", time());
        }
        app('db')->update(static::$table)
            ->data($this->getData())
            ->where([static::$primaryKey => $this->{static::$primaryKey}])
            ->run();
    }

    private function insert()
    {
        if (static::$timestamps === true) {
            $d = date("Y-m-d H:i:s", time());
            $this->{static::$CREATED_AT} = $d;
            $this->{static::$UPDATED_AT} = $d;
        }
        app('db')->insert(static::$table)
            ->data($this->getData())
            ->run();
    }

    private function getData()
    {
        $data = [];
        foreach (static::$attributes as $attr) {
            if (isset($this->$attr))
                $data[$attr] = $this->$attr;
        }
        if (static::$timestamps === true) {
            $data[static::$CREATED_AT] = $this->{static::$CREATED_AT};
            $data[static::$UPDATED_AT] = $this->{static::$UPDATED_AT};
        }
        if (isset(static::$DELETED_AT))
            $data[static::$DELETED_AT] = $this->{static::$DELETED_AT};
        return $data;
    }
}