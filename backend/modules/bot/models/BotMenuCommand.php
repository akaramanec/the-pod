<?php

namespace backend\modules\bot\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * @property int $id
 * @property string $name
 */
class BotMenuCommand extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'bot_menu_command';
    }


    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'trim'],
            [['name'], 'string', 'max' => 255],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    public function getMenu()
    {
        return $this->hasMany(BotMenu::className(), ['command_id' => 'id']);
    }
    public static function listIdName()
    {
        return ArrayHelper::map(self::find()->all(), 'id', 'name');
    }
}
