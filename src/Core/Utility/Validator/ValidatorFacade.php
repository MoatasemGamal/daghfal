<?php

namespace Core\Utility\Validator;

class ValidatorFacade
{
    private $method;
    /**
     * Summary of items
     * @var Item[]
     */
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
    public function input($name, $method = null): Item
    {
        $method = is_null($method) ? $this->method : match ($method) {
            INPUT_POST => INPUT_POST,
            INPUT_GET => INPUT_GET,
            default => INPUT_POST
        };
        $this->items[] = new Item(name: $name, method: $method);
        return end($this->items);
    }
    public function var (array $var)
    {
        $this->items[] = new Item(name: array_key_first($var), value: array_values($var)[0]);
        return end($this->items);
    }
    public function validate(): self
    {
        foreach ($this->items as $item) {
            foreach ($item->rules as $rule) {
                if (method_exists($this, $rule->name))
                    $this->{$rule->name}($item, $rule);
            }
        }
        return $this;
    }
    private function required(Item $item, Rule $rule)
    {
        if (empty($item->value()))
            $this->errors[] = $rule->message;
    }
    private function minMax(Item $item, Rule $rule)
    {
        if (strlen($item->value()) < $rule->params['min'] || strlen($item->value()) > $rule->params['max'])
            $this->errors[] = $rule->message;
    }
}