<?php

namespace backend\modules\shop\models;

use Yii;

/**
 * @property int $id
 * @property string|null $product_id
 * @property string|null $uuid
 * @property string|null $code
 * @property string|null $name
 * @property string|null $slug
 * @property string|null $description
 * @property float|null $price
 */
class ProductModTemp extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'shop_product_mod_temp';
    }

    public function rules()
    {
        return [
            [['product_id'], 'integer'],
            [['description'], 'string'],
            [['price'], 'number'],
            [['characteristics'], 'safe'],
            [['uuid', 'name', 'slug'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 50],
            [['uuid'], 'unique'],
            [['slug'], 'unique'],
        ];
    }

}
