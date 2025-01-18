<?php

namespace src\validators;

use yii\validators\Validator;

class PriceDependencies extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        if ($model->price && !$model->qty) {
            $this->addError($model, 'qty', 'Установите количество');
        }
        return true;
    }
}
