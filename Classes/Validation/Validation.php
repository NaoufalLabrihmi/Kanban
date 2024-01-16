<?php

//namespace Validation ;



class Validation {
    private $errors = [];

    public function validate($key, $value, $rules) {
        foreach ($rules as $rule) {
            // Check for the "int" rule directly
            if ($rule === 'int') {
                if (!is_numeric($value)) {
                    $this->errors[] = "The $key must be an integer.";
                }
            } else {
                // You can add other rules here if needed
            }
        }
    }

    public function getError() {
        return $this->errors;
    }
}
