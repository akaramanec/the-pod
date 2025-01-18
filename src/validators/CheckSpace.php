<?php

namespace src\validators;

use yii\validators\Validator;

class CheckSpace extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $field = explode(' ', $model->$attribute);
        if (count($field) == 1) {
            return true;
        }
        if (count($field) > 1) {
            $this->addError($model, $attribute, 'Пробелы недопустимы');
        }
    }
}
