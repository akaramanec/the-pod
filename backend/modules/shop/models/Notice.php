<?php

namespace backend\modules\shop\models;

use backend\modules\media\models\Images;
use backend\modules\media\models\ImgSave;
use Yii;

/**
 * @property int $id
 * @property string $name
 * @property string $text
 */
class Notice extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE = 3;

    public static function tableName()
    {
        return 'shop_notice';
    }

    public function behaviors()
    {
        return [
            [
                'class' => ImgSave::class,
                'entityImg' => NOTICE
            ]
        ];
    }

    public function rules()
    {
        return [
            [['name', 'text', 'idle_time', 'status'], 'required'],
            [['idle_time', 'status'], 'integer'],
            [['name', 'text'], 'trim'],
            [['text'], 'string'],
            [['name', 'img'], 'string', 'max' => 255],
            [['mainImg'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg', 'maxFiles' => 1],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'text' => 'Текст',
            'idle_time' => 'Дни',
            'status' => 'Статус',
            'mainImg' => 'Загрузить фото',
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

    public function getImages()
    {
        return $this->hasMany(Images::class, ['entity_id' => 'id'])->andWhere(['entity' => NOTICE]);
    }
}
