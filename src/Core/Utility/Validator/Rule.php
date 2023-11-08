<?php

namespace Core\Utility\Validator;

class Rule
{
    public string $name;
    public array $params;
    public string $message;
    public function __construct(string $name, array $params = [], string $message = "sorry, not valid")
    {
        $reflection = new \ReflectionClass(ValidatorFacade::class);
        $availableRules = array_map(fn($reflectionMethod) => $reflectionMethod->name, $reflection->getMethods());
        $this->name = in_array($name, $availableRules) ? $name : "required";
        $this->params = $params;
        $this->message = $message;
    }
}