<?php

namespace Core\Utility\Validator;

class ValidatorFacade
{
    private $method;
    private $items = [];
    public $errors = [];
    public function __construct(int $method = INPUT_POST)
    {
        $this->method = match ($method) {
            INPUT_POST => INPUT_POST,
            INPUT_GET => INPUT_GET,
            default => INPUT_POST
        };
    }
}