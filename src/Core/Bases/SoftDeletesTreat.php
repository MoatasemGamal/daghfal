<?php

namespace Core\Bases;

trait SoftDeletesTreat
{
    protected static string $DELETED_AT = 'deleted_at';

    public function restore(): BaseModel
    {
        $this->{static::$DELETED_AT} = null;
        $this->save();
        return $this;
    }
}