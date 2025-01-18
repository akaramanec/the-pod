<?php

namespace backend\modules\bot\models;

use src\behavior\Timestamp;
use Yii;

/**
 * @property int $id
 * @property array $data
 * @property string $date
 */
class BotLogger extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            [
                'class' => Timestamp::class,
                'create' => ['created_at']
            ],
        ];
    }

    public static function getDb()
    {
        return Yii::$app->get('loggerDb');
    }

    public static function tableName()
    {
        return 'bot_logger';
    }

    public function rules()
    {
        return [
            [['data'], 'required'],
            [['slug'], 'string'],
            [['data', 'created_at'], 'safe'],
        ];
    }

    public static function save_input($data, $slug = null)
    {
        $logger = new BotLogger();
        $logger->data = $data;
        $logger->slug = $slug;
        $logger->insert();
    }


}
