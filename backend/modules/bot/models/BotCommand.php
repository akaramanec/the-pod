<?php

namespace backend\modules\bot\models;

use Yii;

/**
 * This is the model class for table "bot_command".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 */
class BotCommand extends \yii\db\ActiveRecord
{
    const STATUS_NOT_VIEW = 1;
    const STATUS_VIEW = 3;

    public static function tableName()
    {
        return 'bot_command';
    }

    public function rules()
    {
        return [
            [['name', 'status'], 'required'],
            [['status'], 'default', 'value' => self::STATUS_NOT_VIEW],
            [['name'], 'string', 'max' => 50],
            [['name', 'description'], 'trim'],
            [['description'], 'string'],
            ['name', 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
        ];
    }

    public static function status($status)
    {
        switch ($status) {
            case self::STATUS_VIEW:
                $s = '<div class="badge badge-info text-wrap">' . self::statusesAll()[$status] . '</div>';
                break;
            case self::STATUS_NOT_VIEW:
                $s = '<div class="badge badge-danger text-wrap">' . self::statusesAll()[$status] . '</div>';
                break;
        }
        return $s;
    }

    public static function statusesAll()
    {
        return [
            self::STATUS_VIEW => 'View',
            self::STATUS_NOT_VIEW => 'Not View',
        ];
    }

    public static function textTm($name, $data = [])
    {
        $c = self::find()->where(['name' => $name])->asArray()->limit(1)->one();
        if ($c['description']) {
            return strtr($c['description'], $data);
        }
        return 'Отсутствует описание команды';
    }

    public static function textVb($command, $data = [])
    {
        $text = self::textTm($command, $data);
        return preg_replace("/[\r\n]+/", "\n", $text);
    }




}
