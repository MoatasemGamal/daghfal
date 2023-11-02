<?php

namespace Core\Bases;

trait SoftDeletesTreat
{
    protected static string $DELETED_AT = 'deleted_at';
}