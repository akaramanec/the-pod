<?php

namespace backend\modules\bot\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 */
class BotMenu extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'bot_menu';
    }

    public function rules()
    {
        return [
            [['command_id', 'name', 'slug'], 'required'],
            [['command_id'], 'integer'],
            [['name', 'slug'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['name', 'slug'], 'trim'],
            [['slug'], 'unique'],
            [['command_id'], 'exist', 'skipOnError' => true, 'targetClass' => BotMenuCommand::className(), 'targetAttribute' => ['command_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    public function getCommand()
    {
        return $this->hasOne(BotMenuCommand::className(), ['id' => 'command_id']);
    }

    public static function command($name)
    {
        $c = self::find()
            ->where(['name' => $name])
            ->with(['command'])
            ->asArray()->limit(1)->one();
        if ($c) {
            return $c['command']['name'];
        }
    }

    public static function text($slug)
    {
        $c = self::find()->where(['slug' => $slug])->asArray()->limit(1)->one();
        return $c['name'];
    }

    public static function listIdName()
    {
        return ArrayHelper::map(self::find()->all(), 'id', 'name');
    }
}
