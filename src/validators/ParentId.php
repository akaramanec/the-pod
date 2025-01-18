<?php

namespace src\validators;

use src\helpers\CategoryHelp;
use yii\helpers\ArrayHelper;
use yii\validators\Validator;

class ParentId extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $id = CategoryHelp::getChildrenId($model);
        if (ArrayHelper::isIn($model->$attribute, $id)) {
            $this->addError($model, $attribute, 'Нельзя добавить родительскую категорию в дочернюю');
        }
        return true;
    }
}
