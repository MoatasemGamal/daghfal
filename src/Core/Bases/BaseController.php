<?php

namespace Core\Bases;

class BaseController
{
    /**
     * Current Action from Current Controller
     * @var string
     */
    public string $action = '';
    public array $middleWare = [];

    public function registerMiddleware(BaseMiddleware $middleWare)
    {
        $this->middleWare[] = $middleWare;
    }
    /**
     * get middleware array registered to this controller
     * @return BaseMiddleware[]
     */
    public function getMiddleWare(): array
    {
        return $this->middleWare;
    }
}
