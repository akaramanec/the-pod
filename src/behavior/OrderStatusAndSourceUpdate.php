<?php

namespace src\behavior;

use backend\modules\bot\models\Bot;
use backend\modules\bot\telegram\TCommon;
use backend\modules\bot\viber\VCommon;
use backend\modules\shop\models\Order;
use backend\modules\shop\models\OrderTracking;
use backend\modules\shop\service\ProcessingTime;
use src\helpers\Common;
use src\helpers\Date;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/** @property Order $owner */
class OrderStatusAndSourceUpdate extends Behavior
{
    public $success = ['success_at'];
    public $format = 'datetime';

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'resetNew',
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
            ActiveRecord::EVENT_AFTER_INSERT => 'newStatusTracking',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
            ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
        ];
    }

    public function resetNew()
    {
        $statusReset = [
            $this->owner::STATUS_NEW_SITE,
            $this->owner::STATUS_NEW_VIBER,
            $this->owner::STATUS_NEW_TELEGRAM,
            $this->owner::STATUS_NEW_ADMIN
        ];

        if (isset($this->owner) && in_array($this->owner->status, $statusReset)) {
            $this->owner->status = $this->owner::STATUS_NEW;
        }
    }

    public function beforeInsert()
    {
        $this->setSource();
    }

    public function beforeUpdate()
    {
        $this->setSource();
        $this->statusTracking();
        if ($this->owner->status == Order::STATUS_CLOSE_SUCCESS) {
            $this->setSuccessDate();
        }
    }

    private function setSource()
    {
        switch ($this->owner->status) {
            case $this->owner::STATUS_NEW_SITE:
                $this->owner->source = $this->owner::SOURCE_SITE;
                break;
            case $this->owner::STATUS_NEW_VIBER:
                $this->owner->source = $this->owner::SOURCE_VIBER;
                break;
            case $this->owner::STATUS_NEW_TELEGRAM:
                $this->owner->source = $this->owner::SOURCE_TELEGRAM;
                break;
            case $this->owner::STATUS_NEW_ADMIN:
                $this->owner->source = $this->owner::SOURCE_ADMIN;
                break;
        }
    }

    private function setSuccessDate()
    {
        if ($this->success) {
            foreach ($this->success as $item) {
                $this->owner->{$item} = $this->format();
            }
        }
    }

    private function format(): ?string
    {
        switch ($this->format) {
            case 'datetime':
                $f = Date::datetime_now();
                break;
            case 'date':
                $f = Date::date_now();
                break;
        }
        return $f ?? null;
    }

    /**
     * @throws \Exception
     */
    private function statusTracking(): void
    {
        if ($oldThis = Order::findOne($this->owner->id)) {
            if ($oldThis->status != $this->owner->status) {
                $orderTracking = new OrderTracking();
                $orderTracking->order_id = $this->owner->id;
                $orderTracking->manager_id = $this->owner->manager_id;
                $orderTracking->old_status = $oldThis->status;
                $orderTracking->new_status = $this->owner->status;
                $orderTracking->setStepTime($oldThis);
                $orderTracking->save();
            }
        }
    }

    public function newStatusTracking(): void
    {
        $orderTracking = new OrderTracking();
        $orderTracking->order_id = $this->owner->id;
        $orderTracking->new_status = Order::STATUS_NEW;
        $orderTracking->save(false);
    }

    public function beforeDelete()
    {
        OrderTracking::deleteAll(['order_id' => $this->owner->id]);
    }
}