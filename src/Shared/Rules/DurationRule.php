<?php

namespace App\Shared\Rules;

use Yii1x\Validator\Rules\AbstractRule;

class DurationRule extends AbstractRule
{
    public bool $allowEmpty = true;

    protected function validateAttribute(object $object, string $attribute): void
    {
        $value = $object->$attribute;

        if ($this->allowEmpty && $this->isEmpty($value)) {
            return;
        }

        if (!is_string($value)) {
            $this->validator->addError($attribute, $this->message ?? '{attribute} must be a string.');
            return;
        }

        // Форматы: 7h, 20m, 7h 20m, 7h20m
        if (!preg_match('/^(\d+h\s?\d+m|\d+h|\d+m)$/', trim($value))) {
            $this->validator->addError($attribute, $this->message ?? '{attribute} must be in format: 7h, 20m, or 7h 20m.');
        }
    }
}
