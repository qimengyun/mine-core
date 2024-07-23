<?php

namespace Mine;

use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class MineValidation
{
    private array $rules = [];
    private array $scenes = [];
    private array $messages = [];
    private array $customAttributes = [];
    private string $errors;

    public function __construct(
        readonly private ValidatorFactoryInterface $validationFactory
    )
    {
    }

    private function getValidation($validation): void
    {
        $argv = get_class_vars($validation);
        if ($argv) {
            $this->scenes = $argv['scenes'] ?? [];
            $this->rules = $argv['rules'] ?? [];
            $this->messages = $argv['messages'] ?? [];
            $this->customAttributes = $argv['customAttributes'] ?? [];
        }
    }

    public function validation($validation, array $data, $scene = ''): bool
    {
        $this->getValidation($validation);
        $rules = $this->rules;
        if (!($rules && $data)) {
            $this->errors = 'missing parameter';
            return true;
        }
        if ($scene && $this->scenes) {
            $scenes = $this->scenes;
            if (isset($scenes[$scene])) {
                $rules = [];
                $scenes = $scenes[$scene];
                foreach ($scenes as $rule_name) {
                    if (isset($this->rules[$rule_name])) {
                        $rules[$rule_name] = $this->rules[$rule_name];
                    }
                }
            }
        }
        $validator = $this->validationFactory->make($data, $rules, $this->messages, $this->customAttributes);
        if ($validator->fails()) {
            $this->errors = $validator->errors()->first();
            return true;
        }
        return false;
    }

    public function getError(): string
    {
        return $this->errors;
    }
}