<?php

namespace backend\modules\shop\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $status
 * @property string $slug
 * @property int $sort
 */
class Delivery extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE = 3;
    const PICKUP = 'pickup';
    const COURIER_DELIVERY = 'courier-delivery';
    const DELIVERY_NP = 'delivery-np';

    public static function tableName()
    {
        return 'shop_delivery';
    }


    public function rules()
    {
        return [
            [['name', 'status', 'slug'], 'required'],
            [['description'], 'string'],
            [['name', 'description'], 'trim'],
            [['status', 'sort'], 'integer'],
            [['name',], 'string', 'max' => 255],
            [['slug'], 'string', 'max' => 50],
            [['slug'], 'unique'],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'description' => 'Описание',
            'status' => 'Статус',
            'slug' => 'Slug',
            'sort' => 'Sort',
        ];
    }

    public static function bySlug($slug)
    {
        return self::find()->where(['slug' => $slug])->limit(1)->one();
    }

    public static function listSlugName()
    {
        return ArrayHelper::map(self::find()->select(['slug', 'name'])->orderBy('sort asc')->asArray()->all(), 'slug', 'name');
    }

    public static function forOrder()
    {
        return self::find()->indexBy('slug')->orderBy('sort asc')->asArray()->all();
    }
}
