<?php

namespace backend\modules\shop\models;

use Yii;

/**
 * @property int $id
 * @property int|null $category_id
 * @property int|null $qty
 * @property string|null $uuid
 * @property string|null $code
 * @property string|null $name
 * @property string|null $slug
 * @property string|null $description
 * @property float|null $price
 */
class ProductTemp extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'shop_product_temp';
    }


    public function rules()
    {
        return [
            [['category_id', 'qty'], 'integer'],
            [['description'], 'string'],
            [['price'], 'number'],
            [['img'], 'safe'],
            [['uuid', 'name', 'slug'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 50],
            [['uuid'], 'unique'],
            [['slug'], 'unique'],
        ];
    }


}
