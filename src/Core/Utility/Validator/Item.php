<?php

namespace Core\Utility\Validator;

class Item
{
    /**
     * is item variable?
     * false meaning this item is an input
     * @var bool
     */
    public bool $isVar = false;
    /**
     * name of the input or variable
     * @var string
     */
    public string $name;
    /**
     * value of the variable, if this item is variable not (GET, POST) input
     * @var mixed
     */
    public mixed $value;
    /**
     * request method if item is input not variable (INPUT_POST, INPUT_GET)
     * @var int
     */
    public int $method;
    /**
     * rules applied to this item
     * @var \Core\Utility\Validator\Rule[]
     */
    public array $rules = [];
    public function __construct(string $name, mixed $value = null, $method = INPUT_POST)
    {
        $this->name = $name;
        if (!is_null($value)) {
            $this->isVar = true;
            $this->value = $value;
        }
        $this->method = match ($method) {
            INPUT_POST => INPUT_POST,
            INPUT_GET => INPUT_GET,
            default => INPUT_POST
        };
    }
}