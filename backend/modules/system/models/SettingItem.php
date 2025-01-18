<?php

namespace backend\modules\system\models;

use Yii;

/**
 * @property int $setting_id
 * @property string $slug
 * @property string $name
 * @property string $value
 */
class SettingItem extends \yii\db\ActiveRecord
{
    const INTEGER = 'integer';
    const STRING = 'string';
    const FLOAT = 'float';

    public static function tableName()
    {
        return 'setting_item';
    }

    public function rules()
    {
        return [
            [['value'], 'integer', 'on' => 'on_integer'],
            [['value'], 'number', 'on' => 'on_float'],
            [['value'], 'string', 'max' => 255, 'on' => 'on_string'],
            [['setting_id', 'slug', 'name', 'value', 'type'], 'required'],
            [['slug', 'name', 'value'], 'trim'],
            [['setting_id'], 'integer'],
            [['type'], 'string'],
            [['slug', 'name', 'value'], 'string', 'max' => 255],
            [['setting_id', 'slug'], 'unique', 'targetAttribute' => ['setting_id', 'slug']],
            [['setting_id'], 'exist', 'skipOnError' => true, 'targetClass' => Setting::className(), 'targetAttribute' => ['setting_id' => 'id']],
        ];
    }

    public static function primaryKey()
    {
        return ['setting_id', 'slug'];
    }

    public static function listType()
    {
        return [
            self::INTEGER => 'Числовой',
            self::STRING => 'Строка',
            self::FLOAT => 'Номер'
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['on_integer'] = ['value', 'setting_id', 'slug', 'name', 'type'];
        $scenarios['on_string'] = ['value', 'setting_id', 'slug', 'name', 'type'];
        $scenarios['on_float'] = ['value', 'setting_id', 'slug', 'name', 'type'];
        return $scenarios;
    }

    public function scenarioType()
    {
        switch ($this->type) {
            case self::INTEGER:
                $this->scenario = 'on_integer';
                break;
            case self::STRING:
                $this->scenario = 'on_string';
                break;
            case self::FLOAT:
                $this->scenario = 'on_float';
                break;
            default:
                $this->scenario = 'on_string';
        }
    }

    public function attributeLabels()
    {
        return [
            'setting_id' => 'Setting ID',
            'slug' => 'Атрибут',
            'name' => 'Название',
            'value' => 'Значение',
            'type' => 'Тип',
        ];
    }

    public function getSetting()
    {
        return $this->hasOne(Setting::className(), ['id' => 'setting_id']);
    }
}
