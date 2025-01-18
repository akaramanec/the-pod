<?php

namespace backend\modules\notification\models\db;

use backend\modules\customer\models\Customer as BotCustomer;
use Yii;

/**
 * This is the model class for table "bot_notification_customer".
 *
 * @property int $notification_id
 * @property int $customer_id
 *
 * @property BotCustomer $customer
 * @property BotNotification $notification
 */
class BotNotificationCustomer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bot_notification_customer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['notification_id', 'customer_id'], 'required'],
            [['notification_id', 'customer_id'], 'integer'],
            [['notification_id', 'customer_id'], 'unique', 'targetAttribute' => ['notification_id', 'customer_id']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => BotCustomer::className(), 'targetAttribute' => ['customer_id' => 'id']],
            [['notification_id'], 'exist', 'skipOnError' => true, 'targetClass' => BotNotification::className(), 'targetAttribute' => ['notification_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'notification_id' => 'Notification ID',
            'customer_id' => 'Customer ID',
        ];
    }

    /**
     * Gets query for [[Customer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(BotCustomer::className(), ['id' => 'customer_id']);
    }

    /**
     * Gets query for [[Notification]].
     *
     * @return \yii\db\ActiveQuery|\backend\modules\notification\models\query\BotNotificationQuery
     */
    public function getNotification()
    {
        return $this->hasOne(BotNotification::className(), ['id' => 'notification_id']);
    }

    /**
     * {@inheritdoc}
     * @return \backend\modules\notification\models\query\BotNotificationCustomerQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\modules\notification\models\query\BotNotificationCustomerQuery(get_called_class());
    }
}
