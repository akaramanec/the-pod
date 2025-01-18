<?php

namespace backend\modules\bot\models;

use Yii;

/**
 * @property int $id
 * @property string $data
 * @property string|null $slug
 * @property string $created_at
 */
class Logger extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'bot_logger';
    }

    public static function getDb()
    {
        return Yii::$app->get('loggerDb');
    }

    public function rules()
    {
        return [
            [['data'], 'required'],
            [['slug'], 'string'],
            [['data', 'created_at'], 'safe'],
        ];
    }

    public static function commit($data, $slug = null)
    {
        $logger = new BotLogger();
        $logger->data = $data;
        $logger->slug = $slug;
        $logger->created_at = date('Y-m-d H:i:s');
        $logger->insert();
    }


}
