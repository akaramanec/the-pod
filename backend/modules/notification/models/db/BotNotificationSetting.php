<?php

namespace backend\modules\notification\models\db;

use Yii;

/**
 * This is the model class for table "bot_notification_setting".
 *
 * @property int $notification_id
 * @property int $type
 * @property string $value
 *
 * @property BotNotification $notification
 */
class BotNotificationSetting extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bot_notification_setting';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['notification_id', 'type', 'value'], 'required'],
            [['notification_id', 'type'], 'integer'],
            [['value'], 'string', 'max' => 255],
            [['notification_id', 'type'], 'unique', 'targetAttribute' => ['notification_id', 'type']],
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
            'type' => 'Type',
            'value' => 'Value',
        ];
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
     * @return \backend\modules\notification\models\query\BotNotificationSettingQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\modules\notification\models\query\BotNotificationSettingQuery(get_called_class());
    }
}
