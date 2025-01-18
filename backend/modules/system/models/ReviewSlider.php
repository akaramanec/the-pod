<?php

namespace backend\modules\system\models;

use backend\modules\media\models\ImgSave;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $img
 * @property int $status
 */
class ReviewSlider extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE = 3;

    public static function tableName()
    {
        return 'widget_review';
    }

    public function behaviors()
    {
        return [
            [
                'class' => ImgSave::class,
                'entityImg' => REVIEW_SLIDER,
                'sizeOriginal' => 1000
            ],
        ];
    }

    public function rules()
    {
        return [
            [['name', 'status'], 'required'],
            [['description'], 'string'],
            [['name', 'description'], 'trim'],
            [['status', 'sort'], 'integer'],
            ['sort', 'default', 'value' => 100],
            [['name'], 'string', 'max' => 255],
            [['img'], 'string', 'max' => 20],
            [['mainImg'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'description' => 'Описание',
            'img' => 'Img',
            'mainImg' => 'Загрузить фото',
            'status' => 'Статус',
        ];
    }

    public static function status($status)
    {
        switch ($status) {
            case self::STATUS_INACTIVE:
                $s = '<span class="text-danger">' . ArrayHelper::getValue(self::statusesAll(), $status) . '</span>';
                break;
            case self::STATUS_ACTIVE:
                $s = '<span class="text-success">' . ArrayHelper::getValue(self::statusesAll(), $status) . '</span>';
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

}
