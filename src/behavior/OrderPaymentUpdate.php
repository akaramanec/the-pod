<?php

namespace src\behavior;

use backend\modules\shop\models\Order;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/** @property Order $owner */
class OrderPaymentUpdate extends Behavior
{
    const COMMISSION_PERCENTAGE = 2;
    const COMMISSION_UAH = 20;

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_UPDATE => 'checkPaymentMethod',
        ];
    }

    public function checkPaymentMethod()
    {
        if ($this->owner->isNpUponReceipt()) {
            $this->owner->cache_sum_total = ($this->owner->cache_sum_total * (1 + (2 / 100)) + 20);
        }

    }

    /**
     * @param float $sum
     * @return float
     */
    private function getCommissionPrice(float $sum): float
    {
        return $sum * (self::COMMISSION_PERCENTAGE / 100) + self::COMMISSION_UAH;
    }
}