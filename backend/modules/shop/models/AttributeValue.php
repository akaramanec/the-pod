<?php

namespace backend\modules\shop\models;

use backend\modules\customer\models\Customer;
use backend\modules\media\models\Images;
use src\behavior\CapitalLetters;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * @property int $id
 * @property int $attribute_id
 * @property-read mixed $customer
 * @property-read mixed $customerFilter
 * @property-read Attribute $shopAttribute
 * @property-read mixed $attributeValueProductModLink
 * @property-read mixed $images
 * @property-read mixed $img
 * @property-read mixed $product
 * @property string $name
 * @property int $sort [int(10) unsigned]
 */
class AttributeValue extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'shop_attribute_value';
    }

    public function behaviors()
    {
        return [
            [
                'class' => CapitalLetters::class,
                'fields' => ['name'],
            ],
        ];
    }

    public function rules()
    {
        return [
            [['attribute_id', 'name'], 'required'],
            [['attribute_id', 'sort'], 'integer'],
            [['sort'], 'default', 'value' => 100],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['attribute_id'], 'exist', 'skipOnError' => true, 'targetClass' => Attribute::class, 'targetAttribute' => ['attribute_id' => 'id']],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'attribute_id' => 'Attribute ID',
            'name' => 'Name',
        ];
    }

    public function getShopAttribute()
    {
        return $this->hasOne(Attribute::class, ['id' => 'attribute_id']);
    }

    public function getAttributeValueProductModLink()
    {
        return $this->hasMany(AttributeValueProductModLink::class, ['attribute_value_id' => 'id']);
    }

    public function getProduct()
    {
        return $this->hasMany(Product::class, ['id' => 'product_id'])->viaTable('shop_attribute_product_link', ['attribute_value_id' => 'id']);
    }

    public function getImages()
    {
        return $this->hasMany(Images::class, ['entity_id' => 'id'])->andWhere(['entity' => SHOP_ATTRIBUTE_VALUE]);
    }

    public function getCustomerFilter()
    {
        return $this->hasMany(CustomerFilter::class, ['attribute_value_id' => 'id']);
    }

    public function getCustomer()
    {
        return $this->hasMany(Customer::class, ['id' => 'customer_id'])->viaTable('shop_customer_filter', ['attribute_value_id' => 'id']);
    }

    public function getImg()
    {
        return $this->hasOne(Images::class, ['entity_id' => 'id'])->andWhere(['entity' => SHOP_ATTRIBUTE_VALUE])->andWhere(['sort' => 1]);
    }

    public static function byName($name)
    {
        return self::find()->where(['name' => $name])->limit(1)->one();
    }

    public static function asMapByAttribute(Attribute $attribute)
    {
        return ArrayHelper::map(self::find()->where(['attribute_id' => $attribute->id])->orderBy('sort')->all(), 'id', 'name');
    }
}
