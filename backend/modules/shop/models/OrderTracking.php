<?php

namespace backend\modules\shop\models;

use backend\modules\admin\models\AuthAdmin;
use backend\modules\bot\models\Logger;
use backend\modules\shop\service\ProcessingTime;
use DateInterval;
use DateTime;
use src\behavior\Timestamp;
use yii\db\ActiveRecord;

/**
 * @property-read Order $order
 * @property-read AuthAdmin $manager
 * @property int $id [int(11)]
 * @property int $order_id [int(10) unsigned]
 * @property int $manager_id [int(10) unsigned]
 * @property bool $old_status [tinyint(3)]
 * @property bool $new_status [tinyint(3)]
 * @property string $step_time [datetime]
 * @property string $created_at [datetime]
 */
class OrderTracking extends ActiveRecord
{
    const WORK_START_AT = '09:00:00';
    const WORK_FINISH_AT = '21:00:00';

    public static function tableName(): string
    {
        return 'shop_order_status_tracking';
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => Timestamp::class,
                'create' => ['created_at'],
                'update' => false
            ],
        ];
    }

    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    public function getManager()
    {
        return $this->hasOne(AuthAdmin::class, ['id' => 'manager_id']);
    }

    public static function getPrevStep($order_id, $new_status)
    {
        return self::find()
            ->where(['order_id' => $order_id])
            ->andWhere(['new_status' => $new_status])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(1)
            ->one();
    }

    /**
     * @param Order $oldOrder
     * @throws \Exception
     */
    public function setStepTime(Order $oldOrder)
    {
        /** @var self $prevOrderTracking */
        $prevOrderTracking = self::getPrevStep($oldOrder->id, $oldOrder->status);

        if (!$prevOrderTracking) {
            $this->step_time = 0;
        }

        $options = ['startWorkingTime' => self::WORK_START_AT, 'endWorkingTime' => self::WORK_FINISH_AT];
        $processingTimeModel = new ProcessingTime(isset($prevOrderTracking) ? $prevOrderTracking->created_at : date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s'),
            $options
        );
        $this->step_time = $processingTimeModel->workingTimeInterval;
    }

}