<?php

namespace backend\modules\system\models;

use Yii;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 */
class Setting extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'setting';
    }


    public function rules()
    {
        return [
            [['name', 'slug'], 'required'],
            [['name', 'slug'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['slug'], 'unique'],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'slug' => 'Атрибут',
        ];
    }

    public function getSettingItem()
    {
        return $this->hasMany(SettingItem::class, ['setting_id' => 'id']);
    }

    public static function bySlug($slug)
    {
        return self::find()->where(['slug' => $slug])->with(['settingItem'])->limit(1)->one();
    }

    public static function listValue($slug)
    {
        $list = [];
        foreach ((self::bySlug($slug))->settingItem as $item) {
            $list[$item->slug] = $item->value;
        }
        return $list;
    }

    public static function code()
    {
        $code = [];
        foreach (explode(',', Setting::listValue('productsOnMain')['code']) as $item) {
            $code[] = trim($item);
        }
        return $code;
    }
}
