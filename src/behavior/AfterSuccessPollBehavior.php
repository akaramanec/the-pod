<?php

namespace src\behavior;

use backend\modules\shop\models\Poll;
use yii\base\Behavior;
use backend\modules\shop\models\Order;
use backend\modules\shop\models\OrderPoll;
use yii\db\ActiveRecord;

class AfterSuccessPollBehavior extends Behavior
{
    private $orderPoll;

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_UPDATE => 'saveOrderPoll',
        ];
    }

    public function saveOrderPoll()
    {
        if ($poll = Poll::find()->where(['name' => 'afterOrderSuccess'])->limit(1)->one()) {
            if ($this->owner->oldStatus != $this->owner->status && $this->owner->status == Order::STATUS_CLOSE_SUCCESS) {
                $this->setOrderPoll();
                $this->orderPoll->order_id = $this->owner->id;
                $this->orderPoll->poll_id = $poll->id;
                $this->orderPoll->save();
            }
        }
    }

    private function setOrderPoll()
    {
        $orderPoll = OrderPoll::find()
            ->alias('orderPoll')
            ->joinWith('poll as poll')
            ->where(['orderPoll.order_id' => $this->owner->id])
            ->andWhere(['poll.name' => 'afterOrderSuccess'])
            ->limit(1)
            ->one();

        if (!is_null($orderPoll) && $orderPoll->answer_second == null) {
            $this->orderPoll = $orderPoll;
        } else {
            $this->orderPoll = new OrderPoll();
        }
    }
}
