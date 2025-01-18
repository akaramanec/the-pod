<?php

namespace backend\modules\bot\models;

use Yii;

/**
 * @property int $id
 * @property int $sort
 * @property string $slug
 * @property string $text
 * @property string $text_example
 */
class BotPlaceholder extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE = 3;
    public static function tableName()
    {
        return 'bot_placeholder';
    }

    public function rules()
    {
        return [
            [['sort', 'slug'], 'required'],
            [['sort', 'status'], 'integer'],
            [['text', 'text_example'], 'string'],
            [['slug'], 'string', 'max' => 50],
            [['name'], 'string', 'max' => 255],
            [['slug'], 'unique'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'text' => 'Текст',
            'text_example' => 'Пример',
            'status' => 'Статус',
            'sort' => 'Сортировка',
        ];
    }
    public static function status($status)
    {
        switch ($status) {
            case self::STATUS_ACTIVE:
                $s = '<div class="badge badge-success text-wrap">' . self::statusesAll()[$status] . '</div>';
                break;
            case self::STATUS_INACTIVE:
                $s = '<div class="badge badge-danger text-wrap">' . self::statusesAll()[$status] . '</div>';
                break;
        }
        return $s;
    }

    public static function statusesAll()
    {
        return [
            self::STATUS_INACTIVE => 'Не активен',
            self::STATUS_ACTIVE => 'Активен',
        ];
    }

    public static function placeholder($str, $attributeObject)
    {
        $array = [];
        foreach ($attributeObject as $attributeKey => $value) {
            $array['{{' . $attributeKey . '}}'] = $value;
        }
        return strtr($str, $array);
    }

    public static function made($slug, $attributeObject = [])
    {
        return self::placeholder(self::text($slug), $attributeObject);
    }

    public static function text($slug): string
    {
        $pl = BotPlaceholder::find()->where(['slug' => $slug])->limit(1)->one();
        if ($pl) {
            return $pl->text;
        }
        return '';
    }
}
