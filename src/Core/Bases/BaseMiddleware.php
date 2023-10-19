<?php

namespace Core\Bases;

abstract class BaseMiddleware
{
    public array $actions;
    public function __construct(array $actions)
    {
        $this->actions = $actions;
    }
    abstract public function execute();
}
