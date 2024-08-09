<?php

namespace Mine;

use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class MineValidation
{
    private array $rules = [];
    private array $scenes = [];
    private array $messages = [];
    private array $customAttributes = [];
    private array $methods = [];
    private string $errors;
    private bool $status;
    private array $data;

    public function __construct(
        readonly private ValidatorFactoryInterface $validationFactory
    ) {
    }

    private function getValidation($validation): void
    {
        $this->methods = get_class_methods($validation);
        $argv = get_class_vars($validation);
        if ($argv) {
            $this->scenes = $argv['scenes'] ?? [];
            $this->rules = $argv['rules'] ?? [];
            $this->messages = $argv['messages'] ?? [];
            $this->customAttributes = $argv['customAttributes'] ?? [];
        }
    }

    public function validation($validation, array $data, $scene = '')
    {
        $this->getValidation($validation);
        $rules = $this->rules;
        if (!($rules && $data)) {
            $this->errors = 'missing parameter';
            $this->status = true;
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
        $custom_rules = [];
        if ($this->methods) {
            $methods = $this->methods;
            foreach ($rules as $key => $value) {
                $arr = explode('|', $value);
                foreach ($arr as $k => $method) {
                    if (in_array($method, $methods, true)) {
                        $custom_rules[] = [
                            'method' => $method,
                            'value'  => $data[$key],
                            'data'   => $data
                        ];
                        unset($arr[$k]);
                    }
                }
                $rules[$key] = implode('|', $arr);
            }
        }

        $validator = $this->validationFactory->make($data, $rules, $this->messages, $this->customAttributes);
        if ($validator->fails()) {
            $this->errors = $validator->errors()->first();
            $this->status = true;
        } else {
            if ($custom_rules) {
                $validator = new $validation;
                foreach ($custom_rules as $rules) {
                    $method = $rules['method'];
                    $result = $validator->$method($rules['value'], $rules['data']);
                    if (is_bool($result)) {
                        if (!$result) {
                            $this->errors = 'tc:common.error';
                            $this->status = true;
                            return true;
                        }
                    } else {
                        $this->errors = $result;
                        $this->status = true;
                        return true;
                    }
                }
                $this->data = $data;
                $this->status = false;
                return false;
            } else {
                $this->data = $data;
                $this->status = false;
                return false;
            }
        }
    }

    public function getStatus(): array
    {
        $info = [
            'status' => $this->status,
        ];
        if (!$this->status) {
            $info['data'] = $this->data;
        } else {
            $info['errors'] = $this->errors;
        }
        return $info;
    }
}