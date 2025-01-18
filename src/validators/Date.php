<?php

namespace src\validators;

use Yii;
use yii\validators\Validator;

class Date extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $pattern = '/^[0-9]{2}[-]{1}[0-9]{2}[-]{1}[0-9]{4} [0-9]{2}[:]{1}[0-9]{2}$/';
        if (preg_match($pattern, $model->$attribute)) {
            return true;
        } else {
            $this->addError($model, $attribute, 'Неверный формат даты.');
        }
    }
}
