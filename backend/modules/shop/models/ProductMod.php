<?php

namespace backend\modules\shop\models;

use backend\modules\media\models\Img;
use Faker\Provider\Uuid;
use frontend\models\cart\Cart;
use src\helpers\DieAndDumpHelper;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * @property int $id
 * @property int $product_id
 * @property string $uuid
 * @property string|null $code
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property int $status
 * @property float|null $price
 * @property-read AttributeValueProductModLink $attributeValueProductModLink
 * @property-read AttributeValue $attributeBrand
 * @property-read AttributeValue $attributeValue
 * @property-read AttributeValue[] $attributeValues
 * @property-read Product $product
 * @property int $sort
 */
class ProductMod extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE = 3;

    public $attributeValues = [];

    public static function tableName()
    {
        return 'shop_product_mod';
    }

    public function rules()
    {
        return [
            [['product_id', 'uuid', 'name', 'status'], 'required'],
            [['product_id', 'status', 'sort'], 'integer'],
            [['description'], 'string'],
            [['price'], 'number'],
            [['uuid', 'name', 'slug'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 50],
            [['uuid'], 'unique'],
            [['slug'], 'unique'],
            [['code'], 'unique'],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
            ['attributeValues', 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'uuid' => 'Uuid',
            'code' => 'Code',
            'name' => 'Name',
            'slug' => 'Slug',
            'description' => 'Description',
            'status' => 'Status',
            'price' => 'Price',
            'sort' => 'Sort',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->uuidAndSlugGenerate();
            return true;
        }
        return false;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $this->saveAttributeValues();
    }

    protected function saveAttributeValues()
    {
        if (!empty($this->attributeValues)) {
            AttributeValueProductModLink::deleteAll(['mod_id' => $this->id]);
            foreach ($this->attributeValues as $attributeValueId) {
                $link = new AttributeValueProductModLink();
                $link->mod_id = $this->id;
                $link->attribute_value_id = $attributeValueId;
                $link->save();
            }
        }
    }

    public function getAttributeValueProductModLink()
    {
        return $this->hasMany(AttributeValueProductModLink::class, ['mod_id' => 'id']);
    }

    public function getAttributeValues()
    {
        return $this->hasMany(AttributeValue::class, ['id' => 'attribute_value_id'])->viaTable('shop_attribute_value_product_mod_link', ['mod_id' => 'id']);
    }

    public function getAttributeValue($attribute_id)
    {
        return $this->hasOne(AttributeValue::class, ['id' => 'attribute_value_id'])
            ->viaTable('shop_attribute_value_product_mod_link', ['mod_id' => 'id'])
            ->andWhere(['attribute_id' => $attribute_id]);
    }

    public function getAttributeBrand()
    {
        return $this->hasOne(AttributeValue::class, ['id' => 'attribute_value_id'])
            ->viaTable('shop_attribute_value_product_mod_link', ['mod_id' => 'id'])
            ->andWhere(['attribute_id' => Attribute::ID_BRAND]);
    }

    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    public static function getByUuid($uuid)
    {
        return self::find()->where(['uuid' => $uuid])->limit(1)->one();
    }

    public static function bySlug($slug)
    {
        return ProductMod::find()
            ->alias('mod')
            ->where(['product.slug' => $slug])
            ->andWhere(['mod.status' => ProductMod::STATUS_ACTIVE])
            ->joinWith(['product AS product'])
            ->limit(1)
            ->one();
    }

    public static function byId($id)
    {
        return ProductMod::find()
            ->alias('mod')
            ->where(['mod.id' => $id])
            ->andWhere(['mod.status' => ProductMod::STATUS_ACTIVE])
            ->joinWith(['product AS product'])
            ->limit(1)
            ->one();
    }

    public static function byCategory($category_id)
    {
        return self::find()
            ->alias('mod')
            ->where(['product.category_id' => $category_id])
            ->andWhere(['mod.status' => self::STATUS_ACTIVE])
            ->joinWith(['product AS product'])
            ->all();
    }

    public static function byProductCode($code)
    {
        return self::find()
            ->alias('mod')
            ->where(['product.code' => $code])
            ->andWhere(['mod.status' => self::STATUS_ACTIVE])
            ->joinWith(['product AS product'])
            ->limit(1)
            ->one();
    }

    public static function byName($needle)
    {
        return self::find()
            ->alias('mod')
            ->where(['like', 'product.name', '%' . $needle . "%", false])
            ->andWhere(['mod.status' => self::STATUS_ACTIVE])
            ->joinWith(['product AS product'])
            ->all();
    }

    public static function priceMaxMin($mod_id)
    {
        if ($mod_id) {
            $id = implode($mod_id, ',');
            $data = Yii::$app->db->createCommand("SELECT MIN(price), MAX(price)
            FROM shop_product_mod
            WHERE id IN ($id)")->queryOne();
            return [
                'min' => (int)$data['MIN(price)'],
                'max' => (int)$data['MAX(price)']
            ];
        }
        return [
            'min' => 0,
            'max' => 0
        ];
    }

    public static function productData($mod, $platform = 'telegram')
    {
        $img = null;
        if (isset($mod->product->img) && $mod->product->img) {
            if (Img::checkSupportExtension($mod->product->img)) {
                if ($platform == 'telegram') {
                    $img = Img::i($mod->product->img, Yii::$app->params['sizeProduct']['mid_square']);
                } else {
                    $img = Img::iVb($mod->product->img, Yii::$app->params['sizeProduct']['mid_square']);
                }
            } else {
                $img = Img::product($mod->product->img);
            }
        }

        $text = '';
        foreach ($mod->product->attribute_cache as $attribute) {
            $text .= $attribute['name'] . ': ' . implode(', ', $attribute['value']) . PHP_EOL;
        }
        if ($mod->product->description) {
            $text .= $mod->product->description;
        }

        return [
            'text' => $text,
            'img' => $img,
            'name' => $mod->product->name,
            'priceFormat' => Cart::showPriceStatic($mod->product->price),
            'price' => $mod->product->price,
            'mod_id' => $mod->id,
        ];
    }

    public static function searchProductQuery($searchWordsArr, $andCondition = false): \yii\db\ActiveQuery
    {
        $query = self::find()
            ->alias('mod')
            ->joinWith([
                'product AS product',
                'product.category AS category'
            ])
            ->rightJoin('shop_product as p', 'mod.product_id = p.id')
            ->andWhere(['>=', 'p.qty_total', 1]);
        $conditionString = '';
        foreach ($searchWordsArr as $key => $search) {
            $conditionString .= "`mod`.`name` LIKE '%" . trim($search) . "%'";
            if ($key != array_key_last($searchWordsArr)) {
                $conditionString .= " OR ";
            }
        }
        $query->andWhere($conditionString);

        if ($andCondition !== false) {
            $query->andWhere("`mod`.`name` LIKE '%" . trim($andCondition) . "%'");
        }

        $query->andWhere(['mod.status' => ProductMod::STATUS_ACTIVE])
            ->groupBy('p.id');
        return $query;
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE => Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => Yii::t('app', 'Inactive'),
        ];
    }

    public function statusName()
    {
        return static::statuses()[$this->status] ?? '';
    }

    public function statusClass()
    {
        $statusClasses = [
            self::STATUS_ACTIVE => 'success',
            self::STATUS_INACTIVE => 'danger',
        ];

        return $statusClasses[$this->status];
    }

    public function slugGenerate()
    {
        if ($this->isAttributeChanged('name')) {
            $index = 0;
            $this->slug = Inflector::slug($this->name);
            do {
                $slug = $index ? $this->slug . '-' . $index++ : $this->slug;
            } while (ProductMod::find()->where(['slug' => $slug])->exists());
            $this->slug = $slug;
        }
    }

    public function uuidGenerate()
    {
        if (empty($this->uuid)) {
            do {
                $this->uuid = Uuid::uuid();
            } while (ProductMod::find()->where(['uuid' => $this->uuid])->exists());
        }
    }

    public function uuidAndSlugGenerate()
    {
        $this->slugGenerate();
        $this->uuidGenerate();
    }
}
