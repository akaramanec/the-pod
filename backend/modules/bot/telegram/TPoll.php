<?php

namespace backend\modules\bot\telegram;

use backend\modules\shop\models\OrderPoll;
use Yii;

class TPoll extends TCommon
{
    public function answer()
    {
        /** @var OrderPoll $orderPoll */
        $orderPoll = OrderPoll::find()->where(['id' => Yii::$app->tm->data->o_p_id])->limit(1)->one();
        if ($orderPoll->status == $orderPoll::STATUS_FIRST_POLL) {
            $orderPoll->answer_first = Yii::$app->tm->data->a_id;
        } elseif ($orderPoll->status == $orderPoll::STATUS_SECOND_POLL) {
            $orderPoll->answer_second = Yii::$app->tm->data->a_id;
        }
        $text = $this->text('thankYouForMessage') . PHP_EOL;
        $text .= $this->text('start');
        $this->mainMenu($text);
    }
}