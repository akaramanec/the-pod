<?php

namespace src\validators;

use backend\modules\bot\models\BotCommand;
use DateTime;
use yii\validators\Validator;

class IncorrectEndDate extends Validator
{

    public function validateAttribute($model, $attribute)
    {
        if (new DateTime($model->start_at) <= new DateTime($model->$attribute)) {
            return true;
        } else {
            $this->addError($model, $attribute, BotCommand::textTm('invalidEndDate'));
        }
    }

}
