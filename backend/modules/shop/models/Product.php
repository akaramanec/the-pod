<?php

namespace backend\modules\shop\models;

use backend\modules\media\models\Images;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * @property int $id
 * @property int $category_id
 * @property int $code
 * @property string|null $name
 * @property string|null $description
 * @property string $slug
 * @property int $status
 * @property int $qty_total
 */
class Product extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE = 3;

    public static function tableName()
    {
        return 'shop_product';
    }

    public function rules()
    {
        return [
            [['category_id', 'code', 'slug'], 'required'],
            [['category_id', 'status', 'qty_total'], 'integer'],
            [['description'], 'string'],
            [['price'], 'number'],
            [['attribute_cache'], 'safe'],
            [['attribute_cache'], 'default', 'value' => []],
            [['name', 'slug'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 10],
            [['uuid'], 'unique'],
            [['slug'], 'unique'],
            [['code'], 'unique'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Category ID',
            'code' => 'Code',
            'name' => 'Name',
            'description' => 'Description',
            'slug' => 'Slug',
            'status' => 'Status',
            'qty_total' => 'Qty Total',
        ];
    }

    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    public function getMod()
    {
        return $this->hasMany(ProductMod::class, ['product_id' => 'id']);
    }

    public function getImg()
    {
        return $this->hasOne(Images::class, ['entity_id' => 'id'])->andWhere(['entity' => SHOP_PRODUCT])->andWhere(['sort' => 1]);
    }

    public function getImgTwo()
    {
        return $this->hasOne(Images::class, ['entity_id' => 'id'])->andWhere(['entity' => SHOP_PRODUCT])->andWhere(['sort' => 2]);
    }

    public function getImages()
    {
        return $this->hasMany(Images::class, ['entity_id' => 'id'])->andWhere(['entity' => SHOP_PRODUCT]);
    }


    public function saveAttributeCache($product)
    {
        $attributeMod = [];
        $attributeNameUnique = [];
        foreach ($product->mod as $mod) {
            $a = [];
            foreach ($mod->attributeValue as $attributeValue) {
                $attributeNameUnique[$attributeValue->shopAttribute->sort] = $attributeValue->shopAttribute->name;
                $a[$attributeValue->shopAttribute->name] = $attributeValue->name;
            }
            $attributeMod[] = $a;
        }

        $attributeCache = [];
        foreach ($attributeNameUnique as $sort => $attributeName) {
            $array_column = array_column($attributeMod, $attributeName);
            $attributeCache[$sort] = [
                'name' => $attributeName,
                'value' => array_unique($array_column)
            ];
        }
        $product->attribute_cache = $attributeCache;
        $product->save(false);
    }

    public static function attributeByName($attribute_cache, $name)
    {
        foreach ($attribute_cache as $item) {
            if ($item['name'] == $name) {
                return $item['value'];
            }
        }
    }
}
