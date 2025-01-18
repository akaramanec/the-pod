<?php


namespace src\validators;

use Yii;
use yii\validators\Validator;

class WholeNumber extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        if ((is_int($model->$attribute) || ctype_digit($model->$attribute)) && (int)$model->$attribute >= 0) {
            return true;
        } else {
            $this->addError($model, $attribute, 'Процент должен быть целым числом');
        }
    }
}
