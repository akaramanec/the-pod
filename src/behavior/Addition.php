<?php


namespace src\behavior;

use yii\base\Behavior;
use Yii;
use yii\db\ActiveRecord;

class Addition extends Behavior
{

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_UPDATE => 'saveAddition',
        ];
    }

    public function saveAddition()
    {
//        \src\helpers\DieAndDumpHelper::dd(Yii::$app->request->post());
        $post = Yii::$app->request->post('Addition');
        if (isset($post)) {
            $a = [];
            foreach ($post as $key => $item) {
                $a[$key] = trim($item);
            }
            $this->owner->addition = $a;
        }
    }

}
