<?php

namespace backend\modules\notification\models\db;

use Yii;

/**
 * This is the model class for table "bot_notification".
 *
 * @property int $id
 * @property string $name
 * @property string $text
 * @property string|null $img
 * @property int|null $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property BotNotificationSetting[] $botNotificationSettings
 */
class BotNotification extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bot_notification';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'text'], 'required'],
            [['text'], 'string'],
            [['status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['img'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'text' => 'Text',
            'img' => 'Img',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[BotNotificationSettings]].
     *
     * @return \yii\db\ActiveQuery|\backend\modules\notification\models\query\BotNotificationSettingQuery
     */
    public function getBotNotificationSettings()
    {
        return $this->hasMany(BotNotificationSetting::className(), ['notification_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \backend\modules\notification\models\query\BotNotificationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\modules\notification\models\query\BotNotificationQuery(get_called_class());
    }
}
