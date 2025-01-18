<?php

namespace src\behavior;

use backend\modules\bot\src\BloggerWithdrawalRequestService;
use backend\modules\customer\models\Customer;
use backend\modules\shop\models\Order;
use backend\modules\shop\models\OrderPayBloggerFixed;
use blog\models\CacheBloggerFixed;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/** @property Order $owner */
class OrderPaymentBonusesBlogger extends Behavior
{
    const COMMISSION_PERCENTAGE = 2;
    const COMMISSION_UAH = 20;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_UPDATE => 'paymentBonuses',
        ];
    }

    public function paymentBonuses()
    {
        if ($this->owner->status == Order::STATUS_CLOSE_SUCCESS
            && $this->owner->source == Order::SOURCE_TELEGRAM
            && $this->owner->customer->blogger == Customer::BLOGGER_TRUE
            && !empty($this->owner->blogger_bonus)
        ) {
            $customer = $this->owner->customer;
            $model = new OrderPayBloggerFixed();
            $model->customer_id = $customer->id;
            $model->sum = round($this->owner->blogger_bonus);
            if ($model->save()) {
                $cacheBloggerFixed = new CacheBloggerFixed();
                $cacheBloggerFixed->setCache(Customer::findOne($customer->id));
                $model->setData();
                $model->save();
            }
        }

    }

}