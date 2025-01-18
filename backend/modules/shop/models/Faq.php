<?php

namespace backend\modules\shop\models;

use backend\modules\media\models\ImgSave;
use Yii;

/**
 * @property int $id
 * @property string $name
 * @property string|null $text
 * @property int $status
 */
class Faq extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE = 3;

    public static function tableName()
    {
        return 'pod_faq';
    }

    public function behaviors()
    {
        return [
            [
                'class' => ImgSave::class,
                'entityImg' => POD_FAQ
            ]
        ];
    }

    public function rules()
    {
        return [
            [['name', 'text'], 'required'],
            [['text'], 'string'],
            [['status', 'sort'], 'integer'],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['name'], 'string', 'max' => 255],
            [['name', 'text'], 'trim'],
            [['sort'], 'default', 'value' => 100],
            [['img'], 'string', 'max' => 50],
            [['mainImg'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg', 'maxFiles' => 1],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Вопрос',
            'text' => 'Ответ',
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
            self::STATUS_ACTIVE => 'Активен',
            self::STATUS_INACTIVE => 'Не активен',
        ];
    }
}
