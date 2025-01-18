<?php


namespace src\validators;

use backend\modules\bot\models\BotCommand;
use Yii;
use yii\validators\Validator;

class Phone extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $phone = $model->$attribute;
        if ($phone) {
            $pattern_int = '/[^0-9]/';
            $phone = preg_replace($pattern_int, '', $phone);
        }
        $pattern = '/^\d{12}$/';
        if (preg_match($pattern, $phone)) {
            $model->$attribute = $phone;
            return true;
        } else {
            $this->addError($model, $attribute, BotCommand::textTm('errorPhone'));
        }

    }
}
