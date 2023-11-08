<?php

namespace Core\Utility\Validator;

class Rule
{
    public string $name;
    public array $params;
    public string $message;
    public function __construct(string $name, array $params = [], string $message = "sorry, not valid")
    {
        $availableRules = get_class_methods(ValidatorFacade::class);
        $this->name = in_array($name, $availableRules) ? $name : "required";
        $this->params = $params;
        $this->message = $message;
    }

}