<?php

namespace src\validators;

use Exception;
use Yii;

class Is
{
    public static function categoryId()
    {
        return Yii::$app->request->get('id');
    }

    public static function errors($errors)
    {
        if ($errors) {
            foreach ($errors as $item) {
                foreach ($item as $i) {
                    throw new \Exception($i);
                }
            }
        }
    }

    public static function existing($existing)
    {
        if ($existing) {
            throw new Exception(' ');
        }
    }

}
