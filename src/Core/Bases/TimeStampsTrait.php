<?php
namespace Core\Bases;

trait TimeStampsTrait
{
    protected static bool $timestamps = true;
    protected static string $CREATED_AT = 'created_at';
    protected static string $UPDATED_AT = 'updated_at';
}