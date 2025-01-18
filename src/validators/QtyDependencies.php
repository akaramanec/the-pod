<?php

namespace src\validators;

use yii\validators\Validator;

class QtyDependencies extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        if ($model->qty && !$model->price) {
            $this->addError($model, $attribute, 'Для того что бы установить количество, нужно установить цену');
        }
        return true;
    }
}
