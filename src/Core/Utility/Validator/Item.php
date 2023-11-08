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
    private mixed $value;
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
    public function required($message = null): self
    {
        $this->appendRule(name: "required", message: $message);
        return $this;
    }
    public function minMax(int $min, int $max, string $message = null): self
    {
        $this->appendRule(name: "minMax", params: compact("min", "max"), message: $message);
        return $this;
    }
    public function match($otherItem, $inputMethod = INPUT_POST, $message = null): self
    {
        if (is_array($otherItem))
            $item = new Item(name: array_key_first($otherItem), value: array_values($otherItem)[0]);
        else
            $item = new Item(name: $otherItem, method: $inputMethod);
        $this->appendRule(name: "match", params: [$item], message: $message);
        return $this;
    }
    private function appendRule(string $name, array $params = [], ?string $message = "sorry, not valid")
    {
        $message = is_null($message) ? "sorry, not valid" : $message;
        $this->rules[] = new Rule($name, $params, $message);
    }
    public function value(): mixed
    {
        if ($this->isVar)
            return $this->value;
        $inputName = $this->name;
        return match ($this->method) {
            INPUT_GET => $_GET[$inputName],
            INPUT_POST => $_POST[$inputName],
            default => null
        };
    }
}